<?php

namespace App\Http\Controllers\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helper\V1\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\V1\Categories\StoreCategoryRequest;
use App\Http\Requests\V1\Categories\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Constructor to set up middleware.
     * Authentication and authorization required for create, update, destroy.
     * Index and show are publicly accessible.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        $categories = Category::all();

        return ApiResponse::success(
            CategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    /**
     * Display the specified category with its posts.
     */
    public function show($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category)
            return ApiResponse::notFound('category not found.');

        $category->load([
            'posts' => function ($query) {
                $query->where('published_at', '!=', null)
                    ->latest('published_at')
                    ->paginate(10);
            }
        ]);

        return ApiResponse::success(
            new CategoryResource($category),
            'Category retrieved successfully'
        );
    }

    /**
     * Store a newly created category in storage.
     * Authorization is handled by StoreCategoryRequest.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return ApiResponse::success(
            new CategoryResource($category),
            'Category created successfully',
            201
        );
    }

    /**
     * Update the specified category in storage.
     * Authorization is handled by UpdateCategoryRequest.
     */
    public function update(UpdateCategoryRequest $request, $categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category)
            return ApiResponse::notFound('category not found.');

        $category->update($request->validated());

        return ApiResponse::success(
            new CategoryResource($category->fresh()),
            'Category updated successfully',
            200
        );
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Request $request, $categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category)
            return ApiResponse::notFound('category not found.');

        // Check if user is admin or editor
        if (!$request->user()->hasRole(['admin', 'editor'])) {
            return ApiResponse::forbidden('You are not authorized to delete categories.');
        }

        $category->delete();

        return ApiResponse::success(
            [],
            'Category deleted successfully',
            200
        );
    }
}
