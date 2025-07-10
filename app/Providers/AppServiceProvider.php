<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use Spatie\Multitenancy\Models\Tenant;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Tenant::current()) {
                $topSellingProducts = Product::query()
                    ->select('products.*')
                    ->selectSub(function ($query) {
                        $query->from('order_items')
                            ->selectRaw('SUM(quantity)')
                            ->whereColumn('products.id', 'order_items.product_id');
                    }, 'sold_count')
                    ->orderByDesc('sold_count')
                    ->limit(10)
                    ->get();
            } else {
                $topSellingProducts = [];
            }

            $view->with('topSellingProducts', $topSellingProducts);
        });
    }
}
