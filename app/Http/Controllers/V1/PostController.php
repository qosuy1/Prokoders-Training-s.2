<?php

namespace App\Http\Controllers\V1;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Helper\V1\ApiResponse;
use function Laravel\Prompts\error;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\v1\Posts\CreatePostsRequest;

use App\Http\Requests\v1\Posts\updatePostsRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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

        return ApiResponse::success(PostResource::collection($posts), 'Posts retrieved successfully');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(CreatePostsRequest $request)
    {
        try {
            // Check if user has permission to create posts
            $this->authorize('create', Post::class);

            $user = $request->user();
            // Verify user has author record
            if (!$user->author) {
                return ApiResponse::forbidden('You must be an author to create posts.');
            }

            $validated = $request->validated();
            $post = Post::create(
                [
                    'title' => $validated['title'],
                    'body' => $validated['body'],
                    'author_id' => $user->author->id
                ]
            );

            if (!empty($validated['categories_id']))
                $post->categories()->sync($validated['categories_id']);

            return ApiResponse::created(
                new PostResource($post),
                'Post created successfully'
            );
        } catch (HttpResponseException $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    /**
     * Display the specified post with its comments.
     */
    public function show(Request $request, Post $post)
    {
        // Try to authenticate user from Bearer token (even without middleware)
        $user = Auth::guard('sanctum')->user();

        // Debug info
        Log::info('User:', ['user_id' => $user?->id, 'has_author' => $user?->author !== null, 'author_id' => $user?->author?->id, 'post_author_id' => $post->author_id]);

        // Authorize against the post using Gate::forUser() which works with nullable users
        // The view policy method accepts nullable User, so this works for both authenticated and unauthenticated users
        if (!Gate::forUser($user)->allows('view', $post)) {
            abort(403, 'This action is unauthorized.');
        }

        $post->load([
            'comments' => function ($query) {
                $query->latest()->paginate(10);
            }
        ]);

        return ApiResponse::success(new PostResource($post), 'Post retrieved successfully');
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

        if (!empty($validated['categories_id']))
            $post->categories()->attach($validated['categories_id']);

        return ApiResponse::success(
            new PostResource($post->fresh()),
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
        $post->categories()->detach(); //Detach models from the relationship. 

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

        return ApiResponse::success(new PostResource($post->fresh()), 'Post published successfully');
    }
}
