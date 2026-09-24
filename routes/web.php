<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'create'])
    ->name('products.index');

Route::post('/products', [ProductController::class, 'store'])
    ->name('products.store');

/*
|--------------------------------------------------------------------------
| Bulk Image Management
|--------------------------------------------------------------------------
*/

Route::delete('/products/bulk-delete', [ProductController::class, 'bulkDestroy'])
    ->name('products.bulkDestroy');

Route::get('/image-gallery', [ProductController::class, 'gallery'])
    ->name('products.gallery');

/*
|--------------------------------------------------------------------------
| Product Management
|--------------------------------------------------------------------------
*/

Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
    ->name('products.edit');

Route::put('/products/{product}', [ProductController::class, 'update'])
    ->name('products.update');

Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->name('products.destroy');

Route::get('/products/{product}/download', [ProductController::class, 'download'])
    ->name('products.download');