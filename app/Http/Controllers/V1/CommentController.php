<?php

namespace App\Http\Controllers\V1;
use App\Models\Post;


use App\Models\Comment;
use Illuminate\Http\Request;
use App\Helper\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{

    public function postComments($postId)
    {
        $post = Post::find($postId);
        if (!$post) {
            return ApiResponse::notFound('Post not found.');
        }

        $comments = $post->comments;
        return ApiResponse::success(
            CommentResource::collection($comments),
            'all post comments'
        );
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $postId)
    {
        $post = Post::find($postId);
        if (!$post) {
            return ApiResponse::notFound('Post not found.');
        }

        $this->authorize('create', Comment::class);
        $validated = $request->validate([
            'body' => "required|string",
        ]);
        $user = $request->user();
        $validated['post_id'] = $post->id;
        $comment = $user->comments()->create($validated);

        return ApiResponse::success(
            new CommentResource($comment),
            'comment created successfully',
            201
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $postId, $commentId)
    {
        $post = Post::find($postId);
        $comment = Comment::find($commentId);
        if (!$post)
            return ApiResponse::notFound('Post not found.');
        elseif (!$comment)
            return ApiResponse::notFound('Comment not found.');

        $this->authorize('update', $comment);

        $comment = Comment::findOrFail($comment->id);
        $validated = $request->validate([
            'body' => "required|string",
        ]);

        $comment->update($validated);
        return ApiResponse::success(
            new CommentResource($comment),
            'Comment updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($postId, $commentId)
    {
        $post = Post::find($postId);
        $comment = Comment::find($commentId);
        if (!$post)
            return ApiResponse::notFound('Post not found.');
        elseif (!$comment)
            return ApiResponse::notFound('Comment not found.');

        $this->authorize('delete', [$post, $comment]);

        $comment->delete();

        return ApiResponse::success(
            null,
            'Comment deleted successfully'
        );
    }

}
