<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class,'create']);
Route::post('/products',[ProductController::class,'store'])
    ->name('products.store');