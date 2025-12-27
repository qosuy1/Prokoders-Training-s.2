<?php

namespace App\Http\Controllers\V1;

use App\Enums\UserTypeEnum;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Helper\V1\ApiResponse;
use App\Http\Controllers\Controller;
// use Illuminate\Routing\Controller as RoutingController;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(["role:admin,editor", "auth:sanctum"])
            ->except('index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ApiResponse::success(
            Category::get(),
        );
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => "required|string",
            'email' => "required|email"
        ]);

        $category = Category::create($validated);
        return ApiResponse::success(
            $category,
            code: 201
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => "string"
        ]);

        $category = Category::update($validated);
        return ApiResponse::success(
            $category,
            'category updated',
            code: 201
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return ApiResponse::success(null, 'category deleted', 200);
    }
}
