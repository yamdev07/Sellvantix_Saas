<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageSales();
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-]+$/'],
            'email' => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du client est obligatoire.',
            'name.max'      => 'Le nom ne peut pas dépasser 255 caractères.',
            'phone.regex'   => 'Le numéro de téléphone contient des caractères invalides.',
            'phone.max'     => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'email.email'   => "L'adresse email est invalide.",
        ];
    }
}
