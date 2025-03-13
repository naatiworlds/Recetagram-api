<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\PostRepositoryInterface;
use App\Repositories\PostRepository;
use App\Interfaces\IngredientRepositoryInterface;
use App\Repositories\IngredientRepository;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(IngredientRepositoryInterface::class, IngredientRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url)
    {
        if (env('APP_ENV') == 'production') {
            $url->forceScheme('https');
        }
    }
}
