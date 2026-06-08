<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdminOrAdmin() || $this->user()->isManager();
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Le nom de la catégorie est obligatoire.',
            'parent_id.exists' => 'La catégorie parente sélectionnée est invalide.',
            'description.max'  => 'La description ne peut pas dépasser 1 000 caractères.',
        ];
    }
}
