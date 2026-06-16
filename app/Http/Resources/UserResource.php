<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the user into the shape the SPA expects.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified' => ! is_null($this->email_verified_at),
            'email_verified_at' => $this->email_verified_at,
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name')->values(),
            'can' => [
                'manage_cities' => $this->can('manage cities'),
            ],
            'created_at' => $this->created_at,
        ];
    }
}
