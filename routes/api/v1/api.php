<?php

use App\Http\Controllers\Api\V1\Auth\CustomerAuthController;
use App\Http\Controllers\Api\V1\Auth\DeliveryManLoginController;
use App\Http\Controllers\Api\V1\Merchant\Auth\MerchantAuthController;
use App\Http\Controllers\Api\V1\Merchant\Auth\MerchantPasswordResetController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ConfigController;
use App\Http\Controllers\Api\V1\ConversationController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\DeliverymanController;
use App\Http\Controllers\Api\V1\DeliveryManReviewController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\WishlistController;
use App\Http\Controllers\Api\V1\BranchController;//branch
use App\Http\Controllers\Api\V1\MerchantController;
use App\Http\Controllers\Api\V1\Merchant\ServicesController;
use App\Model\Merchant;
use App\Model\Services;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api\V1'], function () {

    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        Route::post('register', [CustomerAuthController::class, 'register']);
        Route::post('login', [CustomerAuthController::class, 'login']);
        Route::post('social-login', [CustomerAuthController::class, 'social_login']);
        Route::post('loginbyphone', [CustomerAuthController::class, 'loginByPhone']);
        Route::post('verify-phone', [CustomerAuthController::class, 'verify_phone']);

        Route::post('check-email', [CustomerAuthController::class, 'check_email']);
        Route::post('verify-email', [CustomerAuthController::class, 'verify_email']);

        Route::post('forgot-password', [PasswordResetController::class, 'reset_password_request']);
        Route::post('verify-token', [PasswordResetController::class, 'verify_token']);
        Route::put('reset-password', [PasswordResetController::class, 'reset_password_submit']);

        Route::group(['prefix' => 'delivery-man'], function () {
            Route::post('login', [DeliveryManLoginController::class, 'login']);
        });
    });

    Route::group(['prefix' => 'merchant-auth', 'namespace' => 'Merchant\Auth'], function () {

        Route::post('register', [MerchantAuthController::class, 'register']);
        Route::post('login', [MerchantAuthController::class, 'login']);
        Route::post('social-login', [MerchantAuthController::class, 'social_login']);
        Route::post('loginbyphone', [MerchantAuthController::class, 'loginByPhone']);
        Route::post('verify-phone', [MerchantAuthController::class, 'verify_phone']);

        Route::post('check-email', [MerchantAuthController::class, 'check_email']);
        Route::post('verify-email', [MerchantAuthController::class, 'verify_email']);

        Route::post('forgot-password', [MerchantPasswordResetController::class, 'reset_password_request']);
        Route::post('verify-token', [MerchantPasswordResetController::class, 'verify_token']);
        Route::put('reset-password', [MerchantPasswordResetController::class, 'reset_password_submit']);
    });

    Route::group(['prefix' => 'merchant', 'middleware' => 'auth:merchant-api'], function () {
       
        Route::group(['prefix' => 'services'], function () {
            Route::get('list', [ServicesController::class, 'get_services']);
            Route::post('store', [ServicesController::class, 'store_services']);
        });
        
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoryController::class, 'get_categories']);
        Route::get('/full', [CategoryController::class, 'get_categories_full']);
        Route::get('childes/{category_id}', [CategoryController::class, 'get_childes']);
        Route::get('products', [CategoryController::class, 'get_products']);
        Route::get('/{category_id}', [CategoryController::class, 'get_category']);
    });

    Route::group(['prefix' => 'delivery-man'], function () {
        Route::get('profile', [DeliverymanController::class, 'get_profile']);
        Route::get('current-orders', [DeliverymanController::class, 'get_current_orders']);
        Route::get('all-orders', [DeliverymanController::class, 'get_all_orders']);
        Route::post('record-location-data', [DeliverymanController::class, 'record_location_data']);
        Route::get('order-delivery-history', [DeliverymanController::class, 'get_order_history']);
        Route::put('update-order-status', [DeliverymanController::class, 'update_order_status']);
        Route::put('update-payment-status', [DeliverymanController::class, 'order_payment_status_update']);
        Route::get('order-details', [DeliverymanController::class, 'get_order_details']);
        Route::get('last-location', [DeliverymanController::class, 'get_last_location']);
        Route::put('update-fcm-token', [DeliverymanController::class, 'update_fcm_token']);

        Route::get('history-list', [DeliverymanController::class, 'get_history_list']);
        Route::post('update-profile', [DeliverymanController::class, 'update_profile']);
        Route::post('update-location', [DeliverymanController::class, 'update_location']);

        Route::group(['prefix' => 'reviews', 'middleware' => ['auth:api']], function () {
            Route::get('/{delivery_man_id}', [DeliveryManReviewController::class, 'get_reviews']);
            Route::get('rating/{delivery_man_id}', [DeliveryManReviewController::class, 'get_rating']);
            Route::post('/submit', [DeliveryManReviewController::class, 'submit_review']);
        });
    });

    Route::group(['prefix' => 'config'], function () {
        Route::get('/', [ConfigController::class, 'configuration']);
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('latest', [ProductController::class, 'get_latest_products']);
        Route::get('set-menu', [ProductController::class, 'get_set_menus']);
        Route::get('search', [ProductController::class, 'get_searched_products']);
        Route::post('searchProduct', [ProductController::class, 'search_products']);
        Route::get('details/{id}', [ProductController::class, 'get_product']);
        Route::get('productDetails/{id}', [ProductController::class, 'get_product_details']);
        Route::get('related-products/{product_id}', [ProductController::class, 'get_related_products']);
        Route::get('reviews/{product_id}', [ProductController::class, 'get_product_reviews']);
        Route::get('rating/{product_id}', [ProductController::class, 'get_product_rating']);
        Route::post('reviews/submit', [ProductController::class, 'submit_product_review'])->middleware('auth:api');
        Route::get('filtered-products', [ProductController::class, 'filtered_routine_products']);

        Route::get('brands', [ProductController::class, 'get_brands']);
        Route::get('brand-categories', [ProductController::class, 'brand_categories']);
        Route::get('brand-products', [ProductController::class, 'get_brand_products']);
        Route::get('suggested-products', [ProductController::class, 'get_suggested_products']);
        Route::get('product-sellers', [ProductController::class, 'product_sellers']);
        //
        Route::get('featured-products', [ProductController::class, 'get_featured_products']);
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('best-seller', [ProductController::class, 'get_best_sellers']);
        Route::get('best-seller-test', [ProductController::class, 'get_best_sellers_test']);
    });

    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', [BannerController::class, 'get_banners']);
    });


    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', [NotificationController::class, 'get_notifications']);
    });


    Route::group(['prefix' => 'customer', 'middleware' => 'auth:api'], function () {

        Route::group(['prefix' => 'wish-list'], function () {
            Route::get('/', [WishlistController::class, 'wish_list']);
            Route::post('add-to-wishlist', [WishlistController::class, 'add_to_wishlist']);
            Route::delete('remove', [WishlistController::class, 'remove_from_wishlist']);
            Route::get('product-ids', [WishlistController::class, 'wishlist_product_ids']);
        });

        Route::group(['prefix' => 'cart'], function () {
            Route::get('/', [CartController::class, 'cart_list']);
            Route::post('add-to-cart', [CartController::class, 'add_to_cart']);
            Route::delete('remove', [CartController::class, 'remove_from_cart']);
            Route::get('product-ids', [CartController::class, 'cart_product_ids']);
        });

        Route::get('info', [CustomerController::class, 'info']);
        Route::put('update-profile', [CustomerController::class, 'update_profile']);
        Route::put('update-name', [CustomerController::class, 'update_name_age']);
        Route::put('update-gift', [CustomerController::class, 'update_gift']);
        Route::put('update-answer', [CustomerController::class, 'update_answer']);
        Route::put('update-version', [CustomerController::class, 'update_version']);
        Route::post('search', [CustomerController::class, 'send_search']);
        Route::post('check-password', [CustomerController::class, 'check_password']);
        Route::post('delete-account', [CustomerController::class, 'delete_account']);
        Route::put('cm-firebase-token', [CustomerController::class, 'update_cm_firebase_token']);
        Route::post('app-review', [CustomerController::class, 'send_app_review']);

        Route::group(['prefix' => 'address'], function () {
            Route::get('list', [CustomerController::class, 'address_list']);
            Route::post('add', [CustomerController::class, 'add_new_address']);
            Route::put('update/{id}', [CustomerController::class, 'update_address']);
            Route::delete('delete', [CustomerController::class, 'delete_address']);
        });


        Route::group(['prefix' => 'order'], function () {
            Route::get('list', [OrderController::class, 'get_order_list']);
            Route::get('running-list', [OrderController::class, 'get_running_orders']);
            Route::get('history-list', [OrderController::class, 'get_history_orders']);
            Route::post('listDetails', [OrderController::class, 'get_order_list_details']);
            Route::get('details', [OrderController::class, 'get_order_details']);
            Route::post('place', [OrderController::class, 'place_order']);
            Route::put('cancel', [OrderController::class, 'cancel_order']);
            Route::get('track', [OrderController::class, 'track_order']);
            Route::put('payment-method', [OrderController::class, 'update_payment_method']);
            Route::get('get-zone', [OrderController::class, 'get_zone']);
        });

        Route::group(['prefix' => 'message'], function () {
            Route::get('get', [ConversationController::class, 'messages']);
            Route::post('send', [ConversationController::class, 'messages_store']);
            Route::post('chat-image', [ConversationController::class, 'chat_image']);
            Route::post('send-image', [ConversationController::class, 'send_image']);
        });

        Route::group(['prefix' => 'wheel'], function () {
            Route::get('wheel-info', [CustomerController::class, 'wheel_info']);
            Route::get('gifts', [CustomerController::class, 'gifts']);
            Route::get('product-gifts', [CustomerController::class, 'product_gifts']);
            Route::get('user-gifts', [CustomerController::class, 'user_gifts']);
            Route::put('update-wheel-limit', [CustomerController::class, 'update_user_wheel_limit']);
            Route::post('request-chances', [CustomerController::class, 'request_chances']);
        });

    });


    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', [BannerController::class, 'get_banners']);
    });


    Route::group(['prefix' => 'coupon', 'middleware' => 'auth:api'], function () {
        Route::get('list', [CouponController::class, 'list']);
        Route::get('apply', [CouponController::class, 'apply']);
    });

    /////////Branch API::

    Route::group(['prefix' => 'branches'], function () {
        Route::get('/', [BranchController::class, 'index']);
        Route::post('/store', [BranchController::class, 'store'])->name('admin.branches.store');
        Route::put('/update/{id}', [BranchController::class, 'update'])->name('admin.branches.update');
        Route::delete('/delete/{id}', [BranchController::class, 'destroy'])->name('admin.branches.destroy');
    });

    /////////////////////


    Route::group(['prefix' => 'merchants'], function () {
        Route::get('nearby-merchants', [MerchantController::class, 'getNearbyMerchants']);
        Route::get('nearby-services-merchants', [MerchantController::class, 'getNearbyServicesMerchants']);
    });

    Route::group(['prefix' => 'drivers'], function () {
        Route::get('nearby', [OrderController::class, 'getNearbyDeliveryMen']);
    });


    Route::group(['prefix' => 'services', 'as' => 'services.'], function () 
    {
            Route::get('list', [ServicesController::class, 'get_services']);
            Route::get('/{category_id}', [ServicesController::class, 'get_service']);
    });

});