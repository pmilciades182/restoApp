<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CategoryTable;
use App\Livewire\CategoryForm;
use App\Livewire\ProductTable;
use App\Livewire\ProductForm;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Categories routes
    Route::get('/categories', CategoryTable::class)->name('categories.index');
    Route::get('/categories/create', CategoryForm::class)->name('categories.create');
    Route::get('/categories/{categoryId}/edit', CategoryForm::class)->name('categories.edit');
    Route::get('/categories/create/{redirect_to?}', CategoryForm::class)->name('categories.create');

    // Products routes
    Route::get('/products', ProductTable::class)->name('products.index');
    Route::get('/products/create', ProductForm::class)->name('products.create');
    Route::get('/products/{productId}/edit', ProductForm::class)->name('products.edit');


});
