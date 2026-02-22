<?php

use Illuminate\Support\Facades\Route;
use Modules\Procurement\Http\Controllers\Api\ProductController;
use Modules\Procurement\Http\Controllers\Api\VariationTemplateController;
use Modules\Procurement\Http\Controllers\Api\BrandController;
use Modules\Procurement\Http\Controllers\Api\UnitController;
use Modules\Procurement\Http\Controllers\Api\CategoryController;
use Modules\Procurement\Http\Controllers\Api\WarrantyController;
use Modules\Procurement\Http\Controllers\Api\SellingPriceGroupController;

/*
|--------------------------------------------------------------------------
| Procurement Module API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with 'api/v1/procurement' and require
| authentication via Passport bearer token.
|
*/

Route::prefix('v1/procurement')->middleware('auth:api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->group(function () {
        Route::get('/',              [ProductController::class, 'index'])->name('procurement.products.index');
        Route::post('/',             [ProductController::class, 'store'])->name('procurement.products.store');

        // Named action routes — must be BEFORE the {id} wildcard
        Route::post('/mass-deactivate', [ProductController::class, 'massDeactivate'])->name('procurement.products.mass-deactivate');
        Route::post('/mass-delete',     [ProductController::class, 'massDelete'])->name('procurement.products.mass-delete');
        Route::post('/check-sku',       [ProductController::class, 'checkSku'])->name('procurement.products.check-sku');

        // Wildcard routes
        Route::get('/{id}',             [ProductController::class, 'show'])->name('procurement.products.show');
        Route::put('/{id}',             [ProductController::class, 'update'])->name('procurement.products.update');
        Route::patch('/{id}',           [ProductController::class, 'update'])->name('procurement.products.patch');
        Route::delete('/{id}',          [ProductController::class, 'destroy'])->name('procurement.products.destroy');
        Route::get('/{id}/stock',       [ProductController::class, 'stock'])->name('procurement.products.stock');
        Route::get('/{id}/variations',  [ProductController::class, 'variations'])->name('procurement.products.variations');
        Route::get('/{id}/group-prices',[ProductController::class, 'groupPrices'])->name('procurement.products.group-prices.index');
        Route::post('/{id}/group-prices',[ProductController::class, 'updateGroupPrices'])->name('procurement.products.group-prices.update');
        Route::patch('/{id}/activate',  [ProductController::class, 'activate'])->name('procurement.products.activate');
    });

    /*
    |--------------------------------------------------------------------------
    | Variation Templates
    |--------------------------------------------------------------------------
    */
    Route::prefix('variation-templates')->group(function () {
        Route::get('/',    [VariationTemplateController::class, 'index'])->name('procurement.variation-templates.index');
        Route::post('/',   [VariationTemplateController::class, 'store'])->name('procurement.variation-templates.store');
        Route::get('/{id}',    [VariationTemplateController::class, 'show'])->name('procurement.variation-templates.show');
        Route::put('/{id}',    [VariationTemplateController::class, 'update'])->name('procurement.variation-templates.update');
        Route::delete('/{id}', [VariationTemplateController::class, 'destroy'])->name('procurement.variation-templates.destroy');
        Route::post('/{id}/values',             [VariationTemplateController::class, 'addValue'])->name('procurement.variation-templates.values.add');
        Route::delete('/{id}/values/{valueId}', [VariationTemplateController::class, 'removeValue'])->name('procurement.variation-templates.values.remove');
    });

    /*
    |--------------------------------------------------------------------------
    | Brands
    |--------------------------------------------------------------------------
    */
    Route::prefix('brands')->group(function () {
        Route::get('/',        [BrandController::class, 'index'])->name('procurement.brands.index');
        Route::post('/',       [BrandController::class, 'store'])->name('procurement.brands.store');
        Route::get('/{id}',    [BrandController::class, 'show'])->name('procurement.brands.show');
        Route::put('/{id}',    [BrandController::class, 'update'])->name('procurement.brands.update');
        Route::delete('/{id}', [BrandController::class, 'destroy'])->name('procurement.brands.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Units
    |--------------------------------------------------------------------------
    */
    Route::prefix('units')->group(function () {
        Route::get('/',        [UnitController::class, 'index'])->name('procurement.units.index');
        Route::post('/',       [UnitController::class, 'store'])->name('procurement.units.store');
        Route::get('/{id}',    [UnitController::class, 'show'])->name('procurement.units.show');
        Route::put('/{id}',    [UnitController::class, 'update'])->name('procurement.units.update');
        Route::delete('/{id}', [UnitController::class, 'destroy'])->name('procurement.units.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */
    Route::prefix('categories')->group(function () {
        Route::get('/',        [CategoryController::class, 'index'])->name('procurement.categories.index');
        Route::post('/',       [CategoryController::class, 'store'])->name('procurement.categories.store');
        Route::get('/{id}',    [CategoryController::class, 'show'])->name('procurement.categories.show');
        Route::put('/{id}',    [CategoryController::class, 'update'])->name('procurement.categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('procurement.categories.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Warranties
    |--------------------------------------------------------------------------
    */
    Route::prefix('warranties')->group(function () {
        Route::get('/',        [WarrantyController::class, 'index'])->name('procurement.warranties.index');
        Route::post('/',       [WarrantyController::class, 'store'])->name('procurement.warranties.store');
        Route::get('/{id}',    [WarrantyController::class, 'show'])->name('procurement.warranties.show');
        Route::put('/{id}',    [WarrantyController::class, 'update'])->name('procurement.warranties.update');
        Route::delete('/{id}', [WarrantyController::class, 'destroy'])->name('procurement.warranties.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Selling Price Groups
    |--------------------------------------------------------------------------
    */
    Route::prefix('selling-price-groups')->group(function () {
        Route::get('/',        [SellingPriceGroupController::class, 'index'])->name('procurement.selling-price-groups.index');
        Route::post('/',       [SellingPriceGroupController::class, 'store'])->name('procurement.selling-price-groups.store');
        Route::get('/{id}',    [SellingPriceGroupController::class, 'show'])->name('procurement.selling-price-groups.show');
        Route::put('/{id}',    [SellingPriceGroupController::class, 'update'])->name('procurement.selling-price-groups.update');
        Route::delete('/{id}', [SellingPriceGroupController::class, 'destroy'])->name('procurement.selling-price-groups.destroy');
        Route::patch('/{id}/toggle-active', [SellingPriceGroupController::class, 'toggleActive'])->name('procurement.selling-price-groups.toggle-active');
    });
});
