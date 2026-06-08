<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdminOrAdmin();
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'phone'   => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'address' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du fournisseur est obligatoire.',
            'name.max'      => 'Le nom ne peut pas dépasser 255 caractères.',
            'phone.regex'   => 'Le numéro de téléphone ne doit contenir que des chiffres.',
            'phone.max'     => 'Le numéro de téléphone ne peut pas dépasser 20 chiffres.',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->phone) {
            $this->merge(['phone' => preg_replace('/[^0-9]/', '', $this->phone)]);
        }
    }
}
