<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'amount'             => 'required|integer|min:1',
            'purchase_price'     => 'nullable|numeric|min:0',
            'sale_price'         => 'nullable|numeric|min:0',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'motif'              => 'nullable|string|max:500',
            'reference_document' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'La quantité à réapprovisionner est obligatoire.',
            'amount.min'      => 'La quantité doit être d\'au moins 1.',
            'amount.integer'  => 'La quantité doit être un nombre entier.',
        ];
    }
}
