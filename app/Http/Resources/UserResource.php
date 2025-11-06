<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'user_type' => $this->user_type,
            'role_id' => $this->role_id,
            'is_active' => (bool) $this->is_active,
            'referral_code' => $this->referral_code,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'phone_verified_at' => $this->phone_verified_at?->toISOString(),
            'subscribed_to_newsletter' => (bool) $this->subscribed_to_newsletter,
            'status' => $this->status,
            'referred_by' => $this->referred_by,
            'dob' => $this->dob?->toISOString(),
            'gender' => $this->gender,
            'city' => $this->city,
            'division' => $this->division,
            'address' => $this->address,
            'remember_token' => $this->remember_token,
            'social_connected' => $this->provider,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
