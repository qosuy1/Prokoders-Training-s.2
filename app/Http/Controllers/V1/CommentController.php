<?php

namespace App\Http\Controllers\V1;
use App\Models\Post;


use App\Models\Comment;
use Illuminate\Http\Request;
use App\Helper\V1\ApiResponse;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{

    public function postComments(Post $post)
    {
        $comments = $post->comments;
        return ApiResponse::success(
            $comments,
            'all post comments'
        );
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('create', Comment::class);

        $validated = $request->validate([
            'body' => "required|text",
        ]);
        $validated['post_id'] = $post->id;
        $validated['user_id'] = $request->user()->id();

        $comment = Comment::create($validated);
        return ApiResponse::success(
            $comment,
            'comment created successfully',
            201
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => "required|text",
        ]);
        $validated['user_id'] = $request->user()->id();

        $comment->update($validated);
        return ApiResponse::success(
            $comment,
            'Comment updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return ApiResponse::success(
            null,
            'Comment deleted successfully'
        );
    }

}
