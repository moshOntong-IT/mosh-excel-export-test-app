<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestExportController;

// Homepage and navigation
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/users', [HomeController::class, 'showUsers'])->name('users.index');
Route::get('/products', [HomeController::class, 'showProducts'])->name('products.index');
Route::get('/orders', [HomeController::class, 'showOrders'])->name('orders.index');

// Basic Export Routes
Route::prefix('export')->name('export.')->group(function () {
    
    // User Exports
    Route::get('users/csv', [TestExportController::class, 'exportUsersCSV'])    
         ->name('users.csv');
    Route::get('users/excel', [TestExportController::class, 'exportVerifiedUsersExcel'])
         ->name('users.excel');
    Route::get('users/interface', [TestExportController::class, 'exportUsersUsingInterface'])
         ->name('users.interface');
    Route::get('users/filtered', [TestExportController::class, 'exportUsersFiltered'])
         ->name('users.filtered');
    
    // Product Exports  
    Route::get('products/category', [TestExportController::class, 'exportProductsByCategory'])
         ->name('products.category');
    Route::get('products/custom', [TestExportController::class, 'exportWithCustomSettings'])
         ->name('products.custom');
    
    // Order Exports
    Route::get('orders/details', [TestExportController::class, 'exportOrdersWithDetails'])
         ->name('orders.details');
    Route::get('orders/items', [TestExportController::class, 'exportOrderItemsWithJoins'])
         ->name('orders.items');
    Route::get('orders/progress', [TestExportController::class, 'exportWithProgress'])
         ->name('orders.progress');
    
    // Special Exports
    Route::get('custom-data', [TestExportController::class, 'exportCustomData'])
         ->name('custom.data');
    Route::get('large-dataset', [TestExportController::class, 'exportLargeDataset'])
         ->name('large.dataset');
    
    // Multi-Sheet Exports (NEW!)
    Route::get('multi-sheet', [TestExportController::class, 'exportMultipleSheets'])
         ->name('multisheet.basic');
    Route::get('multi-sheet/complex', [TestExportController::class, 'exportComplexMultiSheet'])
         ->name('multisheet.complex');
});

// API Routes for testing
Route::prefix('api')->group(function () {
    Route::get('stats', [HomeController::class, 'apiStats'])->name('api.stats');
    
    Route::prefix('export')->group(function () {
        Route::get('users', [TestExportController::class, 'exportUsersCSV']);
        Route::get('products/{category?}', [TestExportController::class, 'exportProductsByCategory']);
        Route::get('orders', [TestExportController::class, 'exportOrdersWithDetails']);
    });
});

// Test Routes with Parameters
Route::get('test/export/users', function () {
    return redirect()->route('export.users.csv');
})->name('test.users');

Route::get('test/export/products/{category?}', function ($category = 'Electronics') {
    return redirect()->route('export.products.category', ['category' => $category]);
})->name('test.products');

Route::get('test/memory', function () {
    return redirect()->route('export.large.dataset');
})->name('test.memory');
