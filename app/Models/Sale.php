<?php

namespace App\Models;

use App\Enums\SaleStatus;
use App\Enums\StockMovementType;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory, TenantScope, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'total_price',
        'discount',
        'tax',
        'final_price',
        'payment_method',
        'payment_status',
        'notes',
        'status',
        'owner_id',
        'tenant_id',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'final_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // ============ RELATIONS ============

    /**
     * Tous les articles de cette vente
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Client associé à cette vente
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Utilisateur (caissier) qui a enregistré la vente
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Propriétaire (super_admin de la quincaillerie)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Tenant (quincaillerie)
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    // ============ ACCESSORS ============

    /**
     * Nombre d'articles dans la vente
     */
    public function getItemCountAttribute()
    {
        return $this->items()->count();
    }

    /**
     * Quantité totale d'articles vendus
     */
    public function getTotalQuantityAttribute()
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Statut de la vente (payée, en attente, annulée)
     */
    public function getStatusLabelAttribute()
    {
        return SaleStatus::tryFrom($this->status ?? SaleStatus::Completed->value)?->label() ?? 'Terminée';
    }

    /**
     * Montant total formaté
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Date formatée
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Récupérer le caissier
     */
    public function getCashierNameAttribute()
    {
        return $this->user?->name ?? 'Inconnu';
    }

    /**
     * Bénéfice de la vente
     */
    public function getProfitAttribute()
    {
        $totalCost = $this->items->sum(function($item) {
            return $item->purchase_price * $item->quantity;
        });
        
        return ($this->final_price ?? $this->total_price) - $totalCost;
    }

    /**
     * Marge bénéficiaire
     */
    public function getProfitMarginAttribute()
    {
        $finalPrice = $this->final_price ?? $this->total_price;
        if ($finalPrice == 0) return 0;
        return ($this->profit / $finalPrice) * 100;
    }

    // ============ SCOPES ============

    /**
     * Filtrer les ventes de la même quincaillerie
     */
    public function scopeSameCompany($query)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Super Admin Global voit tout
            if (method_exists($user, 'isSuperAdminGlobal') && $user->isSuperAdminGlobal()) {
                return $query;
            }
            
            return $query->where('tenant_id', $user->tenant_id);
        }
        return $query;
    }

    /**
     * Ventes d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Ventes de cette semaine
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Ventes de ce mois - Version compatible PostgreSQL
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    /**
     * Ventes d'un client spécifique
     */
    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Ventes d'un caissier spécifique
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Ventes entre deux dates
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Ventes d'un montant minimum
     */
    public function scopeMinAmount($query, $amount)
    {
        return $query->where('total_price', '>=', $amount);
    }

    /**
     * Ventes d'un montant maximum
     */
    public function scopeMaxAmount($query, $amount)
    {
        return $query->where('total_price', '<=', $amount);
    }

    /**
     * Ventes par méthode de paiement
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Ventes par statut de paiement
     */
    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Ventes complétées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', SaleStatus::Completed->value);
    }

    // ============ MÉTHODES ============

    /**
     * Générer un numéro de facture unique
     */
    public function generateInvoiceNumber()
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        
        $lastSale = self::where('tenant_id', $this->tenant_id)
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->orderBy('id', 'desc')
                        ->first();
        
        if ($lastSale && $lastSale->invoice_number) {
            $lastNumber = intval(substr($lastSale->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        $this->invoice_number = "INV-{$year}{$month}-{$newNumber}";
        
        return $this;
    }

    /**
     * Calculer les totaux de la vente
     */
    public function calculateTotals()
    {
        $this->total_price = $this->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        $this->final_price = $this->total_price - ($this->discount ?? 0) + ($this->tax ?? 0);
        
        return $this;
    }

    /**
     * Ajouter un article à la vente
     */
    public function addItem(Product $product, $quantity, $unitPrice = null)
    {
        $unitPrice = $unitPrice ?? $product->sale_price;
        
        $item = $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $unitPrice,
            'purchase_price' => $product->purchase_price,
            'total' => $unitPrice * $quantity,
            'owner_id' => $this->owner_id,
            'tenant_id' => $this->tenant_id,
        ]);

        // Mettre à jour le total de la vente
        $this->updateTotal();

        return $item;
    }

    /**
     * Mettre à jour le montant total de la vente
     */
    public function updateTotal()
    {
        $total = $this->items()->sum('total');
        $this->update(['total_price' => $total, 'final_price' => $total]);
        
        return $this;
    }

    /**
     * Annuler la vente (restaurer le stock)
     */
    public function cancel()
    {
        DB::beginTransaction();
        
        try {
            // Restaurer le stock pour chaque article
            foreach ($this->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                    
                    // Enregistrer le mouvement de stock
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => StockMovementType::Entry->value,
                        'quantity' => $item->quantity,
                        'purchase_price' => $product->purchase_price,
                        'sale_price' => $product->sale_price,
                        'stock_after' => $product->stock + $item->quantity,
                        'motif' => 'Annulation vente #' . $this->id,
                        'reference_document' => 'CANCEL-' . $this->id,
                        'user_id' => auth()->id(),
                        'owner_id' => $this->owner_id,
                        'tenant_id' => $this->tenant_id,
                    ]);
                }
            }

            // Marquer la vente comme annulée
            $this->update(['status' => SaleStatus::Cancelled->value]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Récupérer les statistiques détaillées de la vente
     */
    public function getDetailedStats()
    {
        $items = $this->items()->with('product')->get();
        
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'date' => $this->formatted_date,
            'client' => $this->client?->name ?? 'Client non spécifié',
            'cashier' => $this->cashier_name,
            'total' => $this->formatted_total,
            'item_count' => $this->item_count,
            'total_quantity' => $this->total_quantity,
            'profit' => number_format($this->profit, 0, ',', ' ') . ' FCFA',
            'profit_margin' => round($this->profit_margin, 2) . '%',
            'items' => $items->map(function($item) {
                return [
                    'product' => $item->product?->name ?? 'Produit inconnu',
                    'quantity' => $item->quantity,
                    'unit_price' => number_format($item->price, 0, ',', ' ') . ' FCFA',
                    'total' => number_format($item->total, 0, ',', ' ') . ' FCFA',
                ];
            }),
        ];
    }

    /**
     * Vérifier si la vente peut être modifiée
     */
    public function isEditable()
    {
        // On ne peut modifier que les ventes du jour et non annulées
        return $this->created_at->isToday() && $this->status !== SaleStatus::Cancelled->value;
    }

    /**
     * Dupliquer la vente (pour retour client par exemple)
     */
    public function duplicate()
    {
        $newSale = $this->replicate();
        $newSale->created_at = now();
        $newSale->status = SaleStatus::Pending->value;
        $newSale->invoice_number = null;
        $newSale->save();
        
        $newSale->generateInvoiceNumber();

        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->sale_id = $newSale->id;
            $newItem->save();
        }

        return $newSale;
    }

    /**
     * Récupérer les produits vendus dans cette vente
     */
    public function products()
    {
        return Product::whereIn('id', $this->items()->pluck('product_id'))->get();
    }

    /**
     * Récupérer les statistiques quotidiennes (version optimisée PostgreSQL)
     */
    public static function getDailyStats($date = null, $tenantId = null)
    {
        $date = $date ?? now();
        
        $query = self::whereDate('created_at', $date);
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        return $query->select(
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(final_price) as total_revenue'),
                DB::raw('AVG(final_price) as average_sale'),
                DB::raw('COUNT(DISTINCT client_id) as unique_customers')
            )
            ->first();
    }

    /**
     * Récupérer les meilleures ventes (produits)
     */
    public static function getTopProducts($limit = 10, $tenantId = null, $startDate = null, $endDate = null)
    {
        $query = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->where('sales.status', SaleStatus::Completed->value)
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit);
        
        if ($tenantId) {
            $query->where('sales.tenant_id', $tenantId);
        }
        
        if ($startDate && $endDate) {
            $query->whereBetween('sales.created_at', [$startDate, $endDate]);
        }
        
        return $query->get();
    }
}