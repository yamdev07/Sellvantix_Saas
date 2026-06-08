<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageSales();
    }

    public function rules(): array
    {
        $tenantId = $this->user()->tenant_id;

        return [
            'name'  => 'required|string|max:255',
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('clients', 'email')->where('tenant_id', $tenantId),
            ],
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du client est obligatoire.',
            'email.email'   => 'L\'email doit être une adresse valide.',
            'email.unique'  => 'Un client avec cet email existe déjà dans votre boutique.',
        ];
    }
}
