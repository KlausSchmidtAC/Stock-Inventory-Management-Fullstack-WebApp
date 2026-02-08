<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Actions\GetProductsByCategory;
use App\Actions\GetProduct;
use App\Actions\GetOutOfStockProducts;
use App\Actions\CreateProduct;
use App\Actions\UpdateProduct;
use App\Actions\DeleteProduct;
use App\Actions\DeleteCategory;
use App\Actions\StockAdjustment;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;

// ============================================
// WELCOME PAGE And Authentication Routes
// ============================================

// Route for Guests (Not logged in) - Livewire Component
Route::get('/', Login::class)
    ->middleware('guest')
    ->name('login');

// Route for Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();                           // User ausloggen
    $request->session()->invalidate();        // Session löschen
    $request->session()->regenerateToken();   // CSRF-Token erneuern
    
    return redirect('/');
})->middleware('auth')->name('logout');

// Route for Dashboard (logged in users) - Livewire Component
Route::get('/dashboard', Dashboard::class)
    ->middleware('auth')
    ->name('dashboard');


// ============================================
// PRODUCTION ROUTES (with Authentication)
// ============================================
Route::middleware('auth')->group(function () {
    // Read routes (all authenticated users)
    Route::get('/products/category/{id}', GetProductsByCategory::class)->whereNumber('id');
    Route::get('/products/{id}', GetProduct::class)->whereNumber('id');
    Route::get('/products/out-of-stock', GetOutOfStockProducts::class);
    
    // Stock adjustment (all authenticated users)
    Route::post('/products/stockOperation', StockAdjustment::class);
    
    // Admin/Manager only routes
    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/products', CreateProduct::class);
        Route::put('/products/{id}', UpdateProduct::class)->whereNumber('id');
        Route::delete('/products/{id}', DeleteProduct::class)->whereNumber('id');
        Route::delete('/categories/{id}', DeleteCategory::class)->whereNumber('id');
    });
});

// ============================================
// TEST ROUTES (NO AUTH - Remove before production!)
// ============================================
Route::prefix('test')->middleware('test.admin')->group(function () {
    Route::get('/products/category/{id}', GetProductsByCategory::class)->whereNumber('id');
    Route::get('/products/{id}', GetProduct::class)->whereNumber('id');
    Route::get('/products/out-of-stock', GetOutOfStockProducts::class);
    Route::post('/products/stockOperation', StockAdjustment::class);
    Route::post('/products', CreateProduct::class);
    Route::put('/products/{id}', UpdateProduct::class)->whereNumber('id');
    Route::delete('/products/{id}', DeleteProduct::class)->whereNumber('id');
});


