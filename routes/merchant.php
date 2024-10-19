<?php

use App\Http\Controllers\Merchant\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Merchant\ProductController;
use App\Http\Controllers\Merchant\OrderController;
use App\Http\Controllers\Merchant\CustomerController;
use App\Http\Controllers\Merchant\ZoneController;

Route::group(['namespace' => 'Merchant', 'as' => 'merchant.'], function () 
{
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () 
    {
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit']);
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::group(['middleware' => ['merchant']], function () 
    {
        Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () 
        {
            Route::get('add-new', [ProductController::class, 'index'])->name('add-new');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
            Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');
            Route::post('variant-combination', [ProductController::class, 'variant_combination'])->name('variant-combination');
            Route::post('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [ProductController::class, 'delete_product'])->name('delete');
            Route::post('search', [ProductController::class, 'search'])->name('search');
            Route::get('bulk-import', [ProductController::class, 'bulk_import_index'])->name('bulk-import');
            Route::post('bulk-import', [ProductController::class, 'bulk_import_data']);
            Route::get('bulk-export', [ProductController::class, 'bulk_export_data'])->name('bulk-export');
            Route::get('get-categories', [ProductController::class, 'get_categories'])->name('get-categories');
            Route::post('update-attributes-images/{id}', [ProductController::class, 'update_attributes_images'])->name('update-attributes-images');
            Route::get('attributes-images/{id}', [ProductController::class, 'view_attributes_images'])->name('attributes-images');
            Route::get('delete-attribute/{key}/{product_id}', [ProductController::class, 'delete_attribute'])->name('delete-attribute');
            Route::post('store-attribute', [ProductController::class, 'store_attribute'])->name('store-attribute');
        });



        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () 
        {
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', [OrderController::class, 'add_delivery_man'])->name('add-delivery-man');
            Route::get('payment-status', [OrderController::class, 'payment_status'])->name('payment-status');
            Route::post('productStatus', [OrderController::class, 'productStatus'])->name('productStatus');
            Route::get('generate-invoice/{id}', [OrderController::class, 'generate_invoice'])->name('generate-invoice');
            Route::get('view-invoice/{branch_id}/{order_id}', [OrderController::class, 'view_invoice'])->name('view-invoice');
            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'add_payment_ref_code'])->name('add-payment-ref-code');
            Route::post('search', [OrderController::class, 'search'])->name('search');
            Route::post('update-order-details/{id}', [OrderController::class, 'update_order_details'])->name('update-order-details');
            Route::delete('delete-item/{id}', [OrderController::class, 'delete_item'])->name('delete-item');
            Route::delete('delete-order/{id}', [OrderController::class, 'delete_order'])->name('delete-order');
            Route::get('product-list/{order_id}', [OrderController::class, 'products_list'])->name('product-list');
            Route::post('add-product/{order_id}/{product_id}', [OrderController::class, 'add_product'])->name('add-product');
        });


        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () 
        {
            Route::get('list', [CustomerController::class, 'customer_list'])->name('list');
            Route::get('view/{user_id}', [CustomerController::class, 'view'])->name('view');
            Route::post('search', [CustomerController::class, 'search'])->name('search');
        });


        Route::group(['prefix' => 'zone', 'as' => 'zone.'], function () {
            Route::get('/', [ZoneController::class, 'index'])->name('home');
            Route::get('list', [ZoneController::class, 'list'])->name('list');
            Route::post('store', [ZoneController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ZoneController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ZoneController::class, 'update'])->name('update');
            Route::get('settings/{id}', [ZoneController::class, 'zone_settings'])->name('settings');
            Route::post('zone-settings-update/{id}', [ZoneController::class, 'zone_settings_update'])->name('zone_settings_update');
            Route::delete('delete/{zone}', [ZoneController::class, 'destroy'])->name('delete');
            Route::get('status/{id}/{status}', [ZoneController::class, 'status'])->name('status');
            Route::post('search', [ZoneController::class, 'search'])->name('search');
            Route::get('zone-filter/{id}', [ZoneController::class, 'zone_filter'])->name('zonefilter');
            Route::get('get-all-zone-cordinates/{id?}', [ZoneController::class, 'get_all_zone_cordinates'])->name('zoneCoordinates');
            Route::get('export-zone-cordinates/{type}', [ZoneController::class, 'export_zones'])->name('export-zones');
            Route::delete('destroy-incentive/{id}', [ZoneController::class, 'destroy_incentive'])->name('incentive.destory');
        });


    });
});