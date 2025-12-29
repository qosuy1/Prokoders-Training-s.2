<?php

namespace App\Http\Controllers\V1;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Helper\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Authors\StoreAuthorRequest;
use App\Http\Requests\V1\Authors\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Services\V1\AuthorService;
use Illuminate\Auth\Access\AuthorizationException;
// use Illuminate\Routing\Controller as RoutingController;

class AuthorController extends Controller
{
    private AuthorService $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ApiResponse::success(
            AuthorResource::collection(Author::all()),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        try {
            $this->authorize('create', Author::class);

            $author = $this->authorService->createAuthor($request->user(), $request->validated());

            return ApiResponse::created(new AuthorResource($author), 'Author created successfully');

        } catch (AuthorizationException $e) {
            return ApiResponse::forbidden($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::validationError(['user_id' => [$e->getMessage()]]);
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        return ApiResponse::success(
            new AuthorResource($author),
            'Author retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        try {
            $this->authorize('update', $author);

            $updatedAuthor = $this->authorService->updateAuthor($author, $request->validated());
            return ApiResponse::success(
                new AuthorResource($updatedAuthor),
                'Author updated successfully'
            );
        } catch (AuthorizationException $e) {
            return ApiResponse::forbidden($e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        try {
            $this->authorize('delete', $author);

            $author->delete();
            return ApiResponse::success(
                null,
                'Author deleted successfully'
            );
        } catch (AuthorizationException $e) {
            return ApiResponse::forbidden($e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }


}
