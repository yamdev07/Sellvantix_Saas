<?php

namespace App\Models;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class StockMovement extends Model
{
    use TenantScope;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'purchase_price',
        'sale_price',
        'stock_after',
        'motif',
        'reference_document',
        'user_id',
        'owner_id',
        'tenant_id',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'quantity' => 'integer',
        'stock_after' => 'integer',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];
    
    // ============ RELATIONS ============

    /**
     * Le produit concerné par le mouvement
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * L'utilisateur qui a effectué le mouvement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Propriétaire (super_admin de la quincaillerie)
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Tenant (quincaillerie)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    // ============ ACCESSORS ============

    /**
     * Type de mouvement en français
     */
    public function getTypeLabelAttribute(): string
    {
        return StockMovementType::tryFrom($this->type)?->label() ?? $this->type;
    }

    /**
     * Classe CSS pour le type de mouvement
     */
    public function getTypeClassAttribute(): string
    {
        return StockMovementType::tryFrom($this->type)?->cssClass() ?? '';
    }

    /**
     * Icône pour le type de mouvement
     */
    public function getTypeIconAttribute(): string
    {
        return StockMovementType::tryFrom($this->type)?->icon() ?? '';
    }

    /**
     * Quantité formatée avec signe
     */
    public function getFormattedQuantityAttribute(): string
    {
        $movementType = StockMovementType::tryFrom($this->type);
        return $movementType?->signedQuantity($this->quantity) ?? (string) $this->quantity;
    }

    /**
     * Quantité formatée pour affichage
     */
    public function getDisplayQuantityAttribute(): string
    {
        return $this->quantity . ' unité(s)';
    }

    /**
     * Stock après formaté
     */
    public function getFormattedStockAfterAttribute(): string
    {
        return $this->stock_after . ' unité(s)';
    }

    /**
     * Date formatée
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Prix d'achat formaté
     */
    public function getFormattedPurchasePriceAttribute(): ?string
    {
        return $this->purchase_price ? number_format($this->purchase_price, 0, ',', ' ') . ' FCFA' : null;
    }

    /**
     * Prix de vente formaté
     */
    public function getFormattedSalePriceAttribute(): ?string
    {
        return $this->sale_price ? number_format($this->sale_price, 0, ',', ' ') . ' FCFA' : null;
    }

    /**
     * Référence courte
     */
    public function getShortReferenceAttribute(): string
    {
        if (!$this->reference_document) return 'N/A';
        
        $parts = explode('-', $this->reference_document);
        return end($parts);
    }

    /**
     * Vérifier si c'est une entrée
     */
    public function getIsEntryAttribute(): bool
    {
        return $this->type === StockMovementType::Entry->value;
    }

    /**
     * Vérifier si c'est une sortie
     */
    public function getIsExitAttribute(): bool
    {
        return $this->type === StockMovementType::Exit->value;
    }

    // ============ SCOPES ============

    /**
     * Filtrer les mouvements de la même quincaillerie (par tenant_id)
     * Version compatible PostgreSQL
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
     * Uniquement les entrées
     */
    public function scopeEntries($query)
    {
        return $query->where('type', StockMovementType::Entry->value);
    }

    /**
     * Uniquement les sorties
     */
    public function scopeExits($query)
    {
        return $query->where('type', StockMovementType::Exit->value);
    }

    /**
     * Mouvements d'un produit spécifique
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Mouvements d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Mouvements de cette semaine
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Mouvements de ce mois - Version compatible PostgreSQL
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    /**
     * Mouvements entre deux dates
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Mouvements avec quantité minimum
     */
    public function scopeMinQuantity($query, $quantity)
    {
        return $query->where('quantity', '>=', $quantity);
    }

    /**
     * Mouvements avec un motif spécifique
     */
    public function scopeWithMotif($query, $motif)
    {
        return $query->where('motif', 'LIKE', '%' . $motif . '%');
    }

    /**
     * Mouvements avec une référence
     */
    public function scopeWithReference($query)
    {
        return $query->whereNotNull('reference_document');
    }

    // ============ MÉTHODES ============

    /**
     * Créer une entrée de stock
     */
    public static function createEntry(Product $product, $quantity, $motif, $reference = null, $purchasePrice = null, $salePrice = null)
    {
        DB::beginTransaction();
        
        try {
            $oldStock = $product->stock;
            $newStock = $oldStock + $quantity;
            
            $movement = self::create([
                'product_id' => $product->id,
                'type' => StockMovementType::Entry->value,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice ?? $product->purchase_price,
                'sale_price' => $salePrice ?? $product->sale_price,
                'stock_after' => $newStock,
                'motif' => $motif,
                'reference_document' => $reference,
                'user_id' => auth()->id(),
                'owner_id' => $product->owner_id,
                'tenant_id' => $product->tenant_id,
            ]);

            // Mettre à jour le stock du produit
            $product->update(['stock' => $newStock]);
            
            DB::commit();

            return $movement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Créer une sortie de stock
     */
    public static function createExit(Product $product, $quantity, $motif, $reference = null)
    {
        if ($product->stock < $quantity) {
            throw new InsufficientStockException($product->name, $product->stock, $quantity);
        }

        DB::beginTransaction();
        
        try {
            $oldStock = $product->stock;
            $newStock = $oldStock - $quantity;
            
            $movement = self::create([
                'product_id' => $product->id,
                'type' => StockMovementType::Exit->value,
                'quantity' => $quantity,
                'purchase_price' => $product->purchase_price,
                'sale_price' => $product->sale_price,
                'stock_after' => $newStock,
                'motif' => $motif,
                'reference_document' => $reference,
                'user_id' => auth()->id(),
                'owner_id' => $product->owner_id,
                'tenant_id' => $product->tenant_id,
            ]);

            // Mettre à jour le stock du produit
            $product->update(['stock' => $newStock]);
            
            DB::commit();

            return $movement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Annuler un mouvement de stock
     */
    public function cancel()
    {
        $product = $this->product;
        
        if (!$product) {
            throw new \Exception("Produit non trouvé");
        }

        DB::beginTransaction();
        
        try {
            // Mouvement inverse
            if ($this->type === StockMovementType::Entry->value) {
                if ($product->stock < $this->quantity) {
                    throw new InsufficientStockException($product->name, $product->stock, $this->quantity);
                }
                $product->decrement('stock', $this->quantity);
            } else {
                $product->increment('stock', $this->quantity);
            }

            // Créer un mouvement d'annulation
            $movementType = StockMovementType::tryFrom($this->type);
            $cancelMovement = self::create([
                'product_id' => $this->product_id,
                'type' => $movementType?->opposite()->value ?? StockMovementType::Entry->value,
                'quantity' => $this->quantity,
                'purchase_price' => $this->purchase_price,
                'sale_price' => $this->sale_price,
                'stock_after' => $product->fresh()->stock,
                'motif' => 'ANNULATION: ' . $this->motif,
                'reference_document' => 'CANCEL-' . $this->id,
                'user_id' => auth()->id(),
                'owner_id' => $this->owner_id,
                'tenant_id' => $this->tenant_id,
            ]);
            
            DB::commit();

            return $cancelMovement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Récupérer les détails du mouvement
     */
    public function getDetails(): array
    {
        return [
            'id' => $this->id,
            'product' => $this->product?->name ?? 'Produit inconnu',
            'product_id' => $this->product_id,
            'type' => $this->type_label,
            'type_class' => $this->type_class,
            'type_icon' => $this->type_icon,
            'quantity' => $this->quantity,
            'formatted_quantity' => $this->formatted_quantity,
            'display_quantity' => $this->display_quantity,
            'stock_before' => $this->stock_after - $this->quantity,
            'stock_after' => $this->stock_after,
            'formatted_stock_after' => $this->formatted_stock_after,
            'motif' => $this->motif,
            'reference' => $this->reference_document,
            'short_reference' => $this->short_reference,
            'purchase_price' => $this->formatted_purchase_price,
            'sale_price' => $this->formatted_sale_price,
            'user' => $this->user?->name ?? 'Système',
            'date' => $this->formatted_date,
            'is_entry' => $this->is_entry,
            'is_exit' => $this->is_exit,
        ];
    }

    /**
     * Vérifier si le mouvement peut être annulé
     */
    public function isCancellable(): bool
    {
        // On ne peut annuler que les mouvements récents (moins de 24h)
        return $this->created_at->diffInHours(now()) < 24;
    }

    /**
     * Calculer la valeur du mouvement
     */
    public function getValueAttribute(): float
    {
        if ($this->type === StockMovementType::Entry->value) {
            return $this->quantity * ($this->purchase_price ?? 0);
        }
        return $this->quantity * ($this->sale_price ?? 0);
    }

    /**
     * Valeur formatée
     */
    public function getFormattedValueAttribute(): string
    {
        return number_format($this->value, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Récupérer le prix utilisé (achat pour entrée, vente pour sortie)
     */
    public function getRelevantPriceAttribute(): ?float
    {
        return $this->type === StockMovementType::Entry->value ? $this->purchase_price : $this->sale_price;
    }

    /**
     * Prix pertinent formaté
     */
    public function getFormattedRelevantPriceAttribute(): ?string
    {
        $price = $this->relevant_price;
        return $price ? number_format($price, 0, ',', ' ') . ' FCFA' : null;
    }

    /**
     * Scope pour les statistiques - Version optimisée pour PostgreSQL
     */
    public static function getStats($productId = null, $startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($productId) {
            $query->forProduct($productId);
        }
        
        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        }
        
        $entries = (clone $query)->entries()->sum('quantity');
        $exits = (clone $query)->exits()->sum('quantity');
        
        return [
            'total_entries' => $entries,
            'total_exits' => $exits,
            'net_flow' => $entries - $exits,
            'total_movements' => $query->count(),
        ];
    }

    /**
     * Récupérer les statistiques de mouvement par produit
     * Version optimisée pour PostgreSQL
     */
    public static function getMovementStatsByProduct($tenantId = null, $startDate = null, $endDate = null)
    {
        $query = self::select(
                'product_id',
                DB::raw("SUM(CASE WHEN type = '" . StockMovementType::Entry->value . "' THEN quantity ELSE 0 END) as total_entries"),
                DB::raw("SUM(CASE WHEN type = '" . StockMovementType::Exit->value . "' THEN quantity ELSE 0 END) as total_exits"),
                DB::raw("COUNT(*) as total_movements")
            )
            ->with('product')
            ->groupBy('product_id');
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->orderBy('total_movements', 'desc')->get();
    }

    /**
     * Récupérer les statistiques quotidiennes des mouvements
     * Version optimisée pour PostgreSQL
     */
    public static function getDailyMovementStats($tenantId = null, $days = 30)
    {
        $query = self::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw("SUM(CASE WHEN type = '" . StockMovementType::Entry->value . "' THEN quantity ELSE 0 END) as total_entries"),
                DB::raw("SUM(CASE WHEN type = '" . StockMovementType::Exit->value . "' THEN quantity ELSE 0 END) as total_exits"),
                DB::raw('COUNT(*) as total_movements')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc');
        
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        
        return $query->get();
    }

    /**
     * Vérifier la cohérence des mouvements pour un produit
     */
    public static function checkConsistency(Product $product)
    {
        $totalEntries = self::forProduct($product->id)->entries()->sum('quantity');
        $totalExits = self::forProduct($product->id)->exits()->sum('quantity');
        
        $calculatedStock = $totalEntries - $totalExits;
        
        return [
            'product_name' => $product->name,
            'current_stock' => $product->stock,
            'calculated_stock' => $calculatedStock,
            'total_entries' => $totalEntries,
            'total_exits' => $totalExits,
            'is_consistent' => $product->stock == $calculatedStock,
            'difference' => $product->stock - $calculatedStock,
        ];
    }
}