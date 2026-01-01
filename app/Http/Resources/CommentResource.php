<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user;
        return [
            'user_name' => $user->name,
            'comment_body' => $this->body,
            'created_at' => $this->created_at->format('d-m-y , H:i'),
            'updated_at' => $this->when(
                Carbon::parse($this->created_at) != Carbon::parse($this->updated_at),
                function () {
                    return $this->updated_at->format('d-m-y , H:i');
                }
            ),
            'post' => $this->whenLoaded('post', function () {
                return new PostResource($this->post);
            })
        ];
    }
}
