<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'invoice_number' => $this->invoice_number,
            'status'         => $this->status,
            'status_label'   => $this->status_label,
            'total_price'    => (float) $this->total_price,
            'discount'       => (float) $this->discount,
            'final_price'    => (float) $this->final_price,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'notes'          => $this->notes,
            'client'         => new ClientResource($this->whenLoaded('client')),
            'cashier'        => $this->when(
                $this->relationLoaded('user'),
                fn () => ['id' => $this->user?->id, 'name' => $this->user?->name]
            ),
            'items_count'    => $this->when(
                $this->relationLoaded('items'),
                fn () => $this->items->count()
            ),
            'created_at'     => $this->created_at->toIso8601String(),
        ];
    }
}
