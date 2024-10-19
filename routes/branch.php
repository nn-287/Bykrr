<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Branch', 'as' => 'branch.'], function () {
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        
      Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit');
        Route::get('logout', 'LoginController@logout')->name('logout');
 
        
        Route::get('admin-login', 'AdminLoginController@login')->name('admin-login');
        Route::post('admin-login', 'AdminLoginController@submit');
        Route::get('admin-logout', 'AdminLoginController@logout')->name('admin-logout');
        
        Route::get('staff-login', 'StaffLoginController@login')->name('staff-login');
        Route::post('staff-login', 'StaffLoginController@submit');
        Route::get('staff-logout', 'StaffLoginController@logout')->name('staff-logout');
      
    });
     
    ///// branch admin auth
    Route::group(['middleware' => ['branchadmin']], function () {
         Route::get('/admin-dashboard', 'BranchAdminController@dashboard')->name('admin-dashboard');
      
    });
      
        ///// staff auth
    Route::group(['middleware' => ['merchant']], function () {
        Route::get('staff-dashboard', 'StaffAdminController@dashboard')->name('staff-dashboard'); 
        
        Route::get('/staff-test', 'StaffAdminController@staff_test')->name('staff-test'); 
       
        Route::get('/staff-details', 'StaffAdminController@staff_details')->name('staff-details');
        Route::get('staff-list/{status}', 'OrderController@staff_list')->name('staff-list');
        
         Route::get('/get-staff-data', 'SystemController@staff_data')->name('get-staff-data');
       
    });
    /*authentication*/
 Route::group(['middleware' => ['auth:branch,branch_admin']], function () {
   // Route::group(['middleware' => ['branch']], function () {
        Route::get('/', 'SystemController@dashboard')->name('dashboard');
        Route::get('/get-orders-data', 'SystemController@orders_data')->name('get-orders-data');
        Route::get('/get-admin-data', 'SystemController@admin_data')->name('get-admin-data');

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('status', 'OrderController@status')->name('status');
            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');
            Route::get('payment-status', 'OrderController@payment_status')->name('payment-status');
            Route::post('productStatus', 'OrderController@productStatus')->name('productStatus');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', 'OrderController@add_payment_ref_code')->name('add-payment-ref-code');
        });
        
            Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function () {
            Route::get('invoices-list', 'OrderController@invoices_list')->name('invoices-list');
            Route::get('search', 'OrderController@search_invoice')->name('search');
            Route::get('view/{id}', 'OrderController@view_invoice')->name('view');
        });
        
        Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
            Route::get('add-new', 'ServiceController@add_new')->name('add-new');
            Route::post('store', 'ServiceController@store')->name('store');
            Route::get('edit/{id}', 'ServiceController@edit')->name('edit');
            Route::get('status/{id}/{status}', 'ServiceController@status')->name('status');
            Route::post('update', 'ServiceController@update')->name('update');
            Route::delete('delete/{id}', 'ServiceController@delete')->name('delete');
        });
        
        Route::group(['prefix' => 'booking', 'as' => 'booking.'], function () {
            Route::get('list/{status}', 'BookingController@list')->name('list');
            Route::get('search', 'BookingController@search')->name('search');
            Route::get('details/{id}', 'BookingController@details')->name('details');
            Route::get('view-invoice/{branch_id}/{booking_id}', 'BookingController@view')->name('view-invoice');
            
        });

        Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::get('view/{id}', 'OrderController@view')->name('view');
            Route::post('update-shipping/{id}', 'OrderController@update_shipping')->name('update-shipping');
            Route::post('update-notes/{id}', 'OrderController@update_notes')->name('update-notes');
            Route::delete('delete/{id}', 'OrderController@delete')->name('delete');
            Route::post('search', 'OrderController@search')->name('search');
        });
    });
});
