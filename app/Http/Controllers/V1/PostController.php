<?php

namespace App\Http\Controllers\V1;

use App\Helper\V1\ApiResponse;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Posts\CreatePostsRequest;
use App\Http\Requests\v1\Posts\updatePostsRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Constructor to set up middleware
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy', 'publish']);
    }

    /**
     * Display a paginated listing of all published posts with comments.
     */
    public function index()
    {
        $posts = Post::with('comments')
            ->where('published_at', '!=', null)
            ->latest('published_at')
            ->paginate(15);

        return ApiResponse::success($posts, 'Posts retrieved successfully');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(CreatePostsRequest $request)
    {
        // Check if user has permission to create posts
        $this->authorize('create', Post::class);

        // Verify user has author record
        if (!$request->user()->author) {
            return ApiResponse::forbidden('You must be an author to create posts.');
        }

        $validated = $request->validated();
        $validated['author_id'] = $request->user()->author->id;
        $validated['user_id'] = $request->user()->id;

        $post = Post::create($validated);

        return ApiResponse::success(
            $post->load('comments'),
            'Post created successfully',
            201
        );
    }

    /**
     * Display the specified post with its comments.
     */
    public function show(Post $post)
    {
        // Check authorization using policy
        $this->authorize('view', $post);

        $post = $post->load([
            'comments' => function ($query) {
                $query->latest()->paginate(10);
            }
        ]);

        return ApiResponse::success($post, 'Post retrieved successfully');
    }

    /**
     * Update the specified post in storage.
     */
    public function update(updatePostsRequest $request, Post $post)
    {
        // Check authorization using policy
        $this->authorize('update', $post);

        $validated = $request->validated();
        $post->update($validated);

        return ApiResponse::success(
            $post->fresh(),
            'Post updated successfully'
        );
    }

    /**
     * Remove the specified post and its comments from storage.
     */
    public function destroy(Request $request, Post $post)
    {
        // Check authorization using policy
        $this->authorize('delete', $post);

        // Delete all associated comments
        $post->comments()->delete();

        // Delete the post
        $post->delete();

        return ApiResponse::success([], 'Post deleted successfully');
    }

    /**
     * Publish a post by setting published_at timestamp.
     */
    public function publish(Request $request, Post $post)
    {
        // Check authorization using policy
        $this->authorize('publish', $post);

        // Prevent republishing
        if ($post->published_at) {
            return ApiResponse::validationError(['This post is already published.']);
        }

        $post->update([
            'published_at' => now(),
        ]);

        return ApiResponse::success($post->fresh(), 'Post published successfully');
    }
}
