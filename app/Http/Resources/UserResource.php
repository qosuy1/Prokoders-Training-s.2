<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $retrive =[
            'id' => $this->id,
            'name' =>$this->name,
            'email' =>$this->email,
            'user_role' => $this->getRoleNames()->first(),
        ];
        return $retrive;
    }
}
