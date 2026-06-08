<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->canManageUsers();
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'role'     => 'required|in:admin,manager,cashier,storekeeper',
            'password' => 'nullable|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Le nom est obligatoire.',
            'email.required'    => "L'adresse e-mail est obligatoire.",
            'email.unique'      => 'Cette adresse e-mail est déjà utilisée.',
            'role.required'     => 'Le rôle est obligatoire.',
        ];
    }
}
