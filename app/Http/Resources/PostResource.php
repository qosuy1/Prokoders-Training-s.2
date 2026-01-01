<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'published_at' => isset($this->published_at) ? Carbon::parse($this->published_at)->format('d-m-y , H:i') : null,
            'created_at' => $this->created_at->format('d-m-y , H:i'),
            'updated_at' => $this->when(
                Carbon::parse($this->created_at) != Carbon::parse($this->updated_at),
                function () {
                    return $this->updated_at->format('d-m-y , H:i');
                }
            ),
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'email' => $this->author->email,
            ],
            'comments' => $this->whenLoaded('comments', function () {
                return CommentResource::collection($this->comments);
            })
        ];
    }
}
