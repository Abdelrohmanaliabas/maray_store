<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use Illuminate\Support\ServiceProvider;

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
        view()->composer('store.*', function ($view) {
            $view->with('navCategories', Category::orderBy('sort_order')->orderBy('name')->get());

            /** @var CartService $cart */
            $cart = app(CartService::class);
            $summary = $cart->hydrate();
            $view->with('cartSummary', [
                'count' => $summary['count'] ?? 0,
                'total' => $summary['total'] ?? 0,
            ]);
        });
    }
}
