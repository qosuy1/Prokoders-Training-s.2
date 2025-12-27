<?php

namespace App\Http\Controllers\V1;

use App\Helper\V1\ApiResponse;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Middleware\IsTheUserAuthor;
use App\Http\Requests\v1\Posts\CreatePostsRequest;
use App\Http\Requests\v1\Posts\updatePostsRequest;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])
            ->only(['store', 'update']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ApiResponse::success(
            Post::get(),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePostsRequest $request)
    {
        $request->user()->authorize('create');

        $validated = $request->validated();
        $validated['author_id'] = $request->user()->author->id;
        $post = Post::create($validated);
        return ApiResponse::success(
            $post,
            code: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if (request()->user()->cannot('view', $post))
            return ApiResponse::forbidden();

        return ApiResponse::success(
            $post,
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(updatePostsRequest $request, Post $post)
    {
        if ($request->user()->cannot('update', $post))
            return ApiResponse::forbidden();
        // or
        // $this->authorize('update' , $post);

        $validated = $request->validated();
        $post = Post::update($validated);
        return ApiResponse::success(
            $post,
            'post updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (request()->user()->cannot('delete', $post))
            return ApiResponse::forbidden();

        $post->delete();
        return ApiResponse::success(
            [],
            'post deleted successfully'
        );
    }

    public function publish(Post $post)
    {
        $this->authorize('publish', $post);

        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => 'Post published'
        ]);
    }
}
