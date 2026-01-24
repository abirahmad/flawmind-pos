<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\Api\SellController;
use Modules\Sales\Http\Controllers\Api\PaymentController;
use Modules\Sales\Http\Controllers\Api\SellReturnController;
use Modules\Sales\Http\Controllers\Api\ContactController;

/*
|--------------------------------------------------------------------------
| Sales Module API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with 'api/v1/sales' and require authentication.
|
*/

Route::prefix('v1/sales')->middleware('auth:api')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Sales/Sell Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('sells')->group(function () {
        Route::get('/', [SellController::class, 'index'])->name('sales.sells.index');
        Route::post('/', [SellController::class, 'store'])->name('sales.sells.store');

        // Specific routes must come BEFORE the {id} wildcard route
        Route::get('/drafts', [SellController::class, 'drafts'])->name('sales.sells.drafts');
        Route::get('/quotations', [SellController::class, 'quotations'])->name('sales.sells.quotations');
        Route::post('/quotation', [SellController::class, 'createQuotation'])->name('sales.sells.quotation');
        Route::post('/draft', [SellController::class, 'createDraft'])->name('sales.sells.draft');

        // Wildcard routes
        Route::get('/{id}', [SellController::class, 'show'])->name('sales.sells.show');
        Route::put('/{id}', [SellController::class, 'update'])->name('sales.sells.update');
        Route::delete('/{id}', [SellController::class, 'destroy'])->name('sales.sells.destroy');
        Route::post('/{id}/finalize', [SellController::class, 'finalize'])->name('sales.sells.finalize');
        Route::post('/{id}/convert-to-invoice', [SellController::class, 'convertToInvoice'])->name('sales.sells.convert-to-invoice');
    });

    /*
    |--------------------------------------------------------------------------
    | Payments Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('sales.payments.index');
        Route::post('/', [PaymentController::class, 'store'])->name('sales.payments.store');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('sales.payments.show');
        Route::put('/{id}', [PaymentController::class, 'update'])->name('sales.payments.update');
        Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('sales.payments.destroy');

        // Transaction-specific payments
        Route::get('/transaction/{transactionId}', [PaymentController::class, 'getByTransaction'])
            ->name('sales.payments.by-transaction');
    });

    /*
    |--------------------------------------------------------------------------
    | Sell Returns Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('returns')->group(function () {
        Route::get('/', [SellReturnController::class, 'index'])->name('sales.returns.index');
        Route::post('/', [SellReturnController::class, 'store'])->name('sales.returns.store');
        Route::get('/{id}', [SellReturnController::class, 'show'])->name('sales.returns.show');
        Route::put('/{id}', [SellReturnController::class, 'update'])->name('sales.returns.update');
        Route::delete('/{id}', [SellReturnController::class, 'destroy'])->name('sales.returns.destroy');

        // Get returns for a specific sale
        Route::get('/sale/{saleId}', [SellReturnController::class, 'getBySale'])
            ->name('sales.returns.by-sale');
    });

    /*
    |--------------------------------------------------------------------------
    | Contacts Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('contacts')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('sales.contacts.index');
        Route::post('/', [ContactController::class, 'store'])->name('sales.contacts.store');
        Route::get('/{id}', [ContactController::class, 'show'])->name('sales.contacts.show');
        Route::put('/{id}', [ContactController::class, 'update'])->name('sales.contacts.update');
        Route::delete('/{id}', [ContactController::class, 'destroy'])->name('sales.contacts.destroy');

        // Contact type specific routes
        Route::get('/type/{type}', [ContactController::class, 'getByType'])->name('sales.contacts.by-type');

        // Contact dues
        Route::get('/{id}/dues', [ContactController::class, 'getDues'])->name('sales.contacts.dues');
    });
});
