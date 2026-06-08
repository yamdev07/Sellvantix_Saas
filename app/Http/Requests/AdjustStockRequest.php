<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'adjustment_type'    => 'required|in:add,remove,set',
            'amount'             => 'required|integer|min:0',
            'purchase_price'     => 'nullable|numeric|min:0',
            'sale_price'         => 'nullable|numeric|min:0',
            'reason'             => 'nullable|string|max:500',
            'reference_document' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'adjustment_type.required' => 'Le type d\'ajustement est obligatoire.',
            'adjustment_type.in'       => 'Le type d\'ajustement est invalide.',
            'amount.required'          => 'La quantité est obligatoire.',
            'amount.integer'           => 'La quantité doit être un nombre entier.',
            'amount.min'               => 'La quantité doit être positive.',
        ];
    }
}
