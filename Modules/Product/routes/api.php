<?php

use Modules\Product\Http\Controllers\ProductController;

// API routes for the product module
Route::prefix('products')->group(function () {
    // Route to list all products
    Route::get('/', [ProductController::class, 'list'])->name('products.list');

    // Route to create a new product
    Route::post('/', [ProductController::class, 'create'])->name('products.create');

    // Route to update an existing product
    Route::put('{productId}', [ProductController::class, 'update'])->name('products.update');

    // Route to delete a product
    Route::delete('{productId}', [ProductController::class, 'delete'])->name('products.delete');
});

