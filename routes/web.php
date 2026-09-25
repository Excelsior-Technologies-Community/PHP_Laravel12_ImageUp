<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [ProductController::class, 'create']
)->name('products.index');


/*
|--------------------------------------------------------------------------
| Product Upload
|--------------------------------------------------------------------------
*/

Route::post(
    '/products',
    [ProductController::class, 'store']
)->name('products.store');


/*
|--------------------------------------------------------------------------
| CSV Export
|--------------------------------------------------------------------------
*/

Route::get(
    '/products-export',
    [ProductController::class, 'export']
)->name('products.export');


/*
|--------------------------------------------------------------------------
| Bulk ZIP Download
|--------------------------------------------------------------------------
*/

Route::post(
    '/products/bulk-download',
    [ProductController::class, 'bulkDownload']
)->name('products.bulkDownload');


/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/

Route::delete(
    '/products/bulk-delete',
    [ProductController::class, 'bulkDestroy']
)->name('products.bulkDestroy');


/*
|--------------------------------------------------------------------------
| Clean Missing Images
|--------------------------------------------------------------------------
*/

Route::delete(
    '/products/clean-missing',
    [ProductController::class, 'cleanMissingImages']
)->name('products.cleanMissing');


/*
|--------------------------------------------------------------------------
| Gallery
|--------------------------------------------------------------------------
*/

Route::get(
    '/image-gallery',
    [ProductController::class, 'gallery']
)->name('products.gallery');


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

Route::get(
    '/products/{product}/edit',
    [ProductController::class, 'edit']
)->name('products.edit');


/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

Route::put(
    '/products/{product}',
    [ProductController::class, 'update']
)->name('products.update');


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

Route::delete(
    '/products/{product}',
    [ProductController::class, 'destroy']
)->name('products.destroy');


Route::get(
    '/image-gallery/export',
    [ProductController::class, 'exportGallery']
)->name('products.gallery.export');

Route::post(
    '/image-gallery/download-zip',
    [ProductController::class, 'downloadZip']
)->name('products.gallery.download-zip');


/*
|--------------------------------------------------------------------------
| Download One Image
|--------------------------------------------------------------------------
*/

Route::get(
    '/products/{product}/download',
    [ProductController::class, 'download']
)->name('products.download');