<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CatalogController;
use App\Http\Controllers\Store\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'home'])->name('store.home');
Route::get('/category/{category:slug}', [CatalogController::class, 'category'])->name('store.category');
Route::get('/products/{product:slug}', [CatalogController::class, 'product'])->name('store.product');
Route::get('/products/{product:slug}/variants', [CatalogController::class, 'variants'])->name('store.product.variants');

Route::get('/cart', [CartController::class, 'index'])->name('store.cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('store.cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('store.cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('store.cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('store.cart.clear');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('store.checkout');
Route::post('/checkout', [CheckoutController::class, 'place'])->name('store.checkout.place');
Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('store.order.success');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');

        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [AdminOrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    });
});
