<?php

use Modules\Product\Http\Controllers\ProductController;

Route::prefix('products')->group(function () {
    // Rota para listar todos os produtos
    Route::get('/', [ProductController::class, 'list'])->name('products.list');

    Route::post('/', [ProductController::class, 'create'])->name('teste'); // Test route
    // Rota para atualizar um produto existente
    Route::put('{productId}', [ProductController::class, 'update'])->name('products.update');

    // Rota para excluir um produto
    Route::delete('{productId}', [ProductController::class, 'delete'])->name('products.delete');

});
