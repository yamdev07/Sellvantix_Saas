<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'role'            => $this->role,
            'role_label'      => $this->role_label,
            'is_active'       => (bool) $this->is_active,
            'can_manage_users'=> (bool) $this->can_manage_users,
            'last_login_at'   => $this->last_login_at?->toIso8601String(),
            'created_at'      => $this->created_at->toIso8601String(),
        ];
    }
}
