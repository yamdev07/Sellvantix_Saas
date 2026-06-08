<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'stock'           => $this->stock,
            'stock_alert'     => $this->stock_alert,
            'purchase_price'  => (float) $this->purchase_price,
            'sale_price'      => (float) $this->sale_price,
            'is_low_stock'    => $this->stock > 0 && $this->stock <= ($this->stock_alert ?? 5),
            'is_out_of_stock' => $this->stock === 0,
            'category'        => $this->when(
                $this->relationLoaded('category'),
                fn () => ['id' => $this->category?->id, 'name' => $this->category?->name]
            ),
            'supplier'        => $this->when(
                $this->relationLoaded('supplier'),
                fn () => ['id' => $this->supplier?->id, 'name' => $this->supplier?->name]
            ),
            'created_at'      => $this->created_at->toIso8601String(),
        ];
    }
}
