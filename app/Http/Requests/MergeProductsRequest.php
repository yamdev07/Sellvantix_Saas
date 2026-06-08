<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MergeProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageStock();
    }

    public function rules(): array
    {
        return [
            'product_ids'     => 'required|array|min:2',
            'product_ids.*'   => 'exists:products,id',
            'name'            => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'supplier_id'     => 'required|exists:suppliers,id',
            'batch_reference' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'product_ids.required' => 'Sélectionnez au moins 2 produits à fusionner.',
            'product_ids.min'      => 'Vous devez sélectionner au moins 2 produits.',
            'product_ids.*.exists' => 'Un des produits sélectionnés est invalide.',
            'name.required'        => 'Le nom du produit fusionné est obligatoire.',
            'category_id.required' => 'La catégorie est obligatoire.',
            'supplier_id.required' => 'Le fournisseur est obligatoire.',
        ];
    }
}
