<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category_id' => $this->id,
            'category_name' => $this->name,
            'category_posts' => $this->whenLoaded('posts', function () {
                return PostResource::collection($this->posts);
            })
        ];
    }
}
