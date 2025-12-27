<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Author;
use App\Models\Comment ;
use Illuminate\Http\Request;
use App\Policies\V1\PostPolicy;
use App\Policies\V1\CommentPolicy;
use App\Policies\V1\AuthorPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Author::class, AuthorPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);


        // Requests Rate-limiter X requests per minutes
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(60);
        });
    }
}
