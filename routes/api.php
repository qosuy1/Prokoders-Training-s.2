<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\AuthorController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\CommentController;
use App\Http\Controllers\V1\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')
    ->group(function () {

        // Authetication
        Route::post('register', [AuthController::class, 'register'])
            ->name('user.register');
        Route::post('login', [AuthController::class, 'login'])
            ->name('user.login');
        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum');
        Route::get('user', [AuthController::class, 'show'])
            ->middleware('auth:sanctum');

        // posts
        Route::apiResource('posts', PostController::class);
        Route::post('posts/{postId}/publishPost', [PostController::class, 'publish']);

        // categories
        Route::apiResource('categories', CategoryController::class);

        // comments
        Route::get('/posts/{postId}/comments', [CommentController::class, 'postComments']);
        Route::post('/posts/{postId}/comments', [CommentController::class, 'store'])->middleware('auth:sanctum');
        Route::put('/posts/{postId}/comments/{commentId}', [CommentController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/posts/{postId}/comments/{commentId}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');

        // Author
        Route::apiResource('author', AuthorController::class)->middleware('auth:sanctum');
        // Route::prefix('me')->post('/be-author' , [AuthorController::class, 'beAuthor'])->middleware('auth:sanctum');
    });
