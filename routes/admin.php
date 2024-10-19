<?php

use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DeliveryManController;
use App\Http\Controllers\Admin\LocationSettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OffersController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Admin', 'as' => 'admin.'], function () 
{
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit']);
        
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('home-page', [LoginController::class, 'home_page'])->name('home-page');
        
    });
    /*authentication*/
 /*   Route::group(['namespace' => 'Web', 'prefix' => 'web', 'as' => 'web.'], function () {
        Route::get('home-page', 'LoginController@home_page')->name('home-page');
    });*/
           
        
    Route::group(['middleware' => ['auth:admin,staff']], function () 
    {
        Route::group(['prefix' => 'admin-notification', 'as' => 'admin-notification.'], function () {
            Route::get('list', [AdminNotificationController::class, 'notifications_list'])->name('list');
            Route::get('view/{id}/{category}', [AdminNotificationController::class, 'view'])->name('view');
        });
    
        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::get('add-new', [ProductController::class, 'index'])->name('add-new');
            Route::post('variant-combination', [ProductController::class, 'variant_combination'])->name('variant-combination');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::delete('delete/{id}', [ProductController::class, 'delete_product'])->name('delete');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
            Route::post('search', [ProductController::class, 'search'])->name('search');
            Route::get('bulk-import', [ProductController::class, 'bulk_import_index'])->name('bulk-import');
            Route::post('bulk-import', [ProductController::class, 'bulk_import_data']);
            Route::get('bulk-export', [ProductController::class, 'bulk_export_data'])->name('bulk-export');
            Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            Route::get('get-categories', [ProductController::class, 'get_categories'])->name('get-categories');
            Route::post('update-attributes-images/{id}', [ProductController::class, 'update_attributes_images'])->name('update-attributes-images');
            Route::get('attributes-images/{id}', [ProductController::class, 'view_attributes_images'])->name('attributes-images');
            Route::get('delete-attribute/{key}/{product_id}', [ProductController::class, 'delete_attribute'])->name('delete-attribute');
            Route::post('store-attribute', [ProductController::class, 'store_attribute'])->name('store-attribute');
        });
    
        Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
            Route::get('order', [ReportController::class, 'order_index'])->name('order');
            Route::get('earning', [ReportController::class, 'earning_index'])->name('earning');
            Route::post('set-date', [ReportController::class, 'set_date'])->name('set-date');
            Route::get('analytics', [ReportController::class, 'analytics'])->name('analytics');
            Route::get('wishlists/{latest}', [ReportController::class, 'wishlists'])->name('wishlists');
            Route::get('statistics', [ReportController::class, 'stats_index'])->name('statistics');
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
    
        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
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
    
        Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::put('status-update/{id}', [OrderController::class, 'status'])->name('status-update');
            Route::get('view/{id}', [OrderController::class, 'view'])->name('view');
            Route::post('update-shipping/{id}', [OrderController::class, 'update_shipping'])->name('update-shipping');
            Route::delete('delete/{id}', [OrderController::class, 'delete'])->name('delete');
        });
    
        Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
            Route::get('list', [ConversationController::class, 'list'])->name('list');
            Route::post('store/{user_id}', [ConversationController::class, 'store'])->name('store');
            Route::get('view/{user_id}', [ConversationController::class, 'view'])->name('view');
            Route::get('view-refresh/{user_id}', [ConversationController::class, 'view_refresh'])->name('view-refresh');
            Route::get('services-list', [ConversationController::class, 'services_list'])->name('services-list');
            Route::post('services-store/{user_id}', [ConversationController::class, 'services_store'])->name('services-store');
            Route::get('services-view/{user_id}', [ConversationController::class, 'services_view'])->name('services-view');
            Route::post('store-product-message/{user_id}', [ConversationController::class, 'store_product_message'])->name('store-product-message');
            Route::get('services-view-refresh/{user_id}', [ConversationController::class, 'services_view_refresh'])->name('services-view-refresh');
        });
    });
    
 
    Route::group(['middleware' => ['admin']], function () 
    {
        Route::get('/', [SystemController::class, 'dashboard'])->name('dashboard');
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settings_update']);
        Route::post('settings-password', [SystemController::class, 'settings_password_update'])->name('settings-password');
        Route::get('/get-store-data', [SystemController::class, 'store_data'])->name('get-store-data');

        Route::group(['prefix' => 'banner', 'as' => 'banner.'], function () {
            Route::get('add-new', [BannerController::class, 'index'])->name('add-new');
            Route::post('store', [BannerController::class, 'store'])->name('store');
            Route::get('edit/{id}', [BannerController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [BannerController::class, 'update'])->name('update');
            Route::get('list', [BannerController::class, 'list'])->name('list');
            Route::get('status/{id}/{status}', [BannerController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [BannerController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
            Route::get('add-new', [AttributeController::class, 'index'])->name('add-new');
            Route::post('store', [AttributeController::class, 'store'])->name('store');
            Route::get('edit/{id}', [AttributeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [AttributeController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [AttributeController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'offers', 'as' => 'offers.', 'middleware' => ['admin']], function () {
            Route::get('add-new', [OffersController::class, 'index'])->name('add-new');
            Route::post('store', [OffersController::class, 'store'])->name('store');
            Route::get('edit/{id}', [OffersController::class, 'edit'])->name('edit');
            Route::post('update', [OffersController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [OffersController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'store', 'as' => 'store.'], function () {
            // Route::get('add-new', [StoreCont::class, 'index'])->name('add-new');
            // Route::post('store', [StoreController::class, 'store'])->name('store');
            // Route::get('edit/{id}', [StoreController::class, 'edit'])->name('edit');
            // Route::post('update/{id}', [StoreController::class, 'update'])->name('update');
            // Route::delete('delete/{id}', [StoreController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.'], function () {
            Route::get('add', [DeliveryManController::class, 'index'])->name('add');
            Route::post('store', [DeliveryManController::class, 'store'])->name('store');
            Route::get('list', [DeliveryManController::class, 'list'])->name('list');
            Route::get('preview/{id}', [DeliveryManController::class, 'preview'])->name('preview');
            Route::get('edit/{id}', [DeliveryManController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [DeliveryManController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [DeliveryManController::class, 'delete'])->name('delete');
            Route::post('search', [DeliveryManController::class, 'search'])->name('search');

            Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
                Route::get('list', [DeliveryManController::class, 'reviews_list'])->name('list');
            });
        });

        Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
            Route::get('add-new', [NotificationController::class, 'index'])->name('add-new');
            Route::post('store', [NotificationController::class, 'store'])->name('store');
            Route::get('edit/{id}', [NotificationController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [NotificationController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [NotificationController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [NotificationController::class, 'delete'])->name('delete');
            Route::get('add-new-filter', [NotificationController::class, 'filter_index'])->name('add-new-filter');
            Route::post('specific', [NotificationController::class, 'notifyFilter'])->name('specific');
            Route::get('new-message-notification/{user_id}', [NotificationController::class, 'chat_index'])->name('new-message-notification');
            Route::get('new-message-notification-se/{user_id}', [NotificationController::class, 'services_chat_index'])->name('new-message-notification-se');
            Route::post('store-msg-notification', [NotificationController::class, 'store_msg_notification'])->name('store-msg-notification');
            Route::get('filter-notify', [NotificationController::class, 'userFilterId'])->name('filter-notify');
        });

        Route::group(['prefix' => 'service', 'as' => 'service.'], function () {
            Route::get('add', [ServiceController::class, 'index'])->name('add');
            Route::post('store', [ServiceController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ServiceController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ServiceController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [ServiceController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [ServiceController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('add', [CategoryController::class, 'index'])->name('add');
            Route::get('add-sub-category', [CategoryController::class, 'sub_index'])->name('add-sub-category');
            Route::get('add-sub-sub-category', [CategoryController::class, 'sub_sub_index'])->name('add-sub-sub-category');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [CategoryController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CategoryController::class, 'delete'])->name('delete');
            Route::post('search', [CategoryController::class, 'search'])->name('search');
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
            Route::get('list', [ReviewsController::class, 'list'])->name('list');
            Route::post('search', [ReviewsController::class, 'search'])->name('search');
        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
            Route::get('add-new', [CouponController::class, 'add_new'])->name('add-new');
            Route::post('store', [CouponController::class, 'store'])->name('store');
            Route::get('update/{id}', [CouponController::class, 'edit'])->name('update');
            Route::post('update/{id}', [CouponController::class, 'update']);
            Route::get('status/{id}/{status}', [CouponController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CouponController::class, 'delete'])->name('delete');

            Route::group(['prefix' => 'custom', 'as' => 'custom.'], function () {
                Route::get('edit', [CouponController::class, 'edit_custom_coupon'])->name('edit');
                Route::post('update', [CouponController::class, 'update_custom_coupon'])->name('update');
            });
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function () {
            Route::get('store-setup', [BusinessSettingsController::class, 'store_index'])->name('store-setup');
            Route::post('update-setup', [BusinessSettingsController::class, 'store_setup'])->name('update-setup');

            Route::get('fcm-index', [BusinessSettingsController::class, 'fcm_index'])->name('fcm-index');
            Route::post('update-fcm', [BusinessSettingsController::class, 'update_fcm'])->name('update-fcm');
            Route::post('update-fcm-messages', [BusinessSettingsController::class, 'update_fcm_messages'])->name('update-fcm-messages');

            Route::get('mail-config', [BusinessSettingsController::class, 'mail_index'])->name('mail-config');
            Route::post('mail-config', [BusinessSettingsController::class, 'mail_config']);

            Route::get('payment-method', [BusinessSettingsController::class, 'payment_index'])->name('payment-method');
            Route::post('payment-method-update/{payment_method}', [BusinessSettingsController::class, 'payment_update'])->name('payment-method-update');

            Route::get('currency-add', [BusinessSettingsController::class, 'currency_index'])->name('currency-add');
            Route::post('currency-add', [BusinessSettingsController::class, 'currency_store']);
            Route::get('currency-update/{id}', [BusinessSettingsController::class, 'currency_edit'])->name('currency-update');
            Route::put('currency-update/{id}', [BusinessSettingsController::class, 'currency_update']);
            Route::delete('currency-delete/{id}', [BusinessSettingsController::class, 'currency_delete'])->name('currency-delete');

            Route::get('terms-and-conditions', [BusinessSettingsController::class, 'terms_and_conditions'])->name('terms-and-conditions');
            Route::post('terms-and-conditions', [BusinessSettingsController::class, 'terms_and_conditions_update']);

            Route::get('privacy-policy', [BusinessSettingsController::class, 'privacy_policy'])->name('privacy-policy');
            Route::post('privacy-policy', [BusinessSettingsController::class, 'privacy_policy_update']);

            Route::get('about-us', [BusinessSettingsController::class, 'about_us'])->name('about-us');
            Route::post('about-us', [BusinessSettingsController::class, 'about_us_update']);

            Route::get('location-setup', [LocationSettingsController::class, 'location_index'])->name('location-setup');
            Route::post('update-location', [LocationSettingsController::class, 'location_setup'])->name('update-location');

            Route::get('/app-version', [BusinessSettingsController::class, 'app_version'])->name('app-version');
            Route::post('force-update', [BusinessSettingsController::class, 'force_update'])->name('force-update');
        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
            Route::get('list', [CustomerController::class, 'customer_list'])->name('list');
            Route::get('view/{user_id}', [CustomerController::class, 'view'])->name('view');
            Route::post('search', [CustomerController::class, 'search'])->name('search');
        });



        Route::group(['prefix' => 'branch', 'as' => 'branch.'], function () { // list/edit/create/delete/store
            Route::get('list', [BranchController::class, 'list'])->name('list');
            Route::get('add-new', [BranchController::class, 'add_new'])->name('add-new'); // view
            Route::post('store', [BranchController::class, 'store'])->name('store'); // store in db
            Route::get('edit/{id}', [BranchController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [BranchController::class, 'update'])->name('update'); // store in db
            Route::match(['post', 'get'], 'status/{id}',[BranchController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [BranchController::class, 'destroy'])->name('delete');
        });

    });
});
