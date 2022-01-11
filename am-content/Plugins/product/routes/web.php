<?php

Route::group(['as' => 'admin.shop.', 'prefix' => 'admin/shop', 'namespace' => 'Amcoders\Plugin\product\http\controllers', 'middleware' => 'web', 'auth', 'admin'], function () {

    Route::resource('category', 'CategoryController');
    Route::post('categories/destroy', 'CategoryController@destroy')->name('productcategory');

});




Route::group(['as' => 'admin.', 'prefix' => 'admin/', 'namespace' => 'Amcoders\Plugin\product\http\controllers', 'middleware' => 'web', 'auth', 'admin'], function () {
    Route::get('/basic_email', 'ProductController@basic_email');
    Route::get('/test_jul', 'ProductController@test_jul')->name('product.test.jul');
    Route::get('/test_inspiration', 'ProductController@test_inspiration')->name('product.test.inspiration');
    Route::get('/test_kott', 'ProductController@test_kott')->name('product.test.kott');
    Route::get('/test_vegetariskt', 'ProductController@test_vegetariskt')->name('product.test.vegetariskt');
    Route::get('/test_mejeri', 'ProductController@test_mejeri')->name('product.test.mejeri');
    Route::get('/test_skafferi', 'ProductController@test_skafferi')->name('product.test.skafferi');
    Route::get('/test_frukt', 'ProductController@test_frukt')->name('product.test.frukt');
    Route::get('/test_barn', 'ProductController@test_barn')->name('product.test.barn');
    Route::get('/test_brod', 'ProductController@test_brod')->name('product.test.brod');
    Route::get('/test_fryst', 'ProductController@test_fryst')->name('product.test.fryst');
    Route::get('/test_fardigmat', 'ProductController@test_fardigmat')->name('product.test.fardigmat');
    Route::get('/test_dryck', 'ProductController@test_dryck')->name('product.test.dryck');
    Route::get('/test_glass', 'ProductController@test_glass')->name('product.test.glass');
    Route::get('/test_stad', 'ProductController@test_stad')->name('product.test.stad');
    Route::get('/test_halsa', 'ProductController@test_halsa')->name('product.test.halsa');
    Route::get('/test_djur', 'ProductController@test_djur')->name('product.test.djur');
    Route::get('/test_blommor', 'ProductController@test_blommor')->name('product.test.blommor');
    Route::get('/test_kok', 'ProductController@test_kok')->name('product.test.kok');
    Route::get('/test_hem', 'ProductController@test_hem')->name('product.test.hem');
    Route::get('/test_fritid', 'ProductController@test_fritid')->name('product.test.fritid');
    Route::get('/test_receptfria', 'ProductController@test_receptfria')->name('product.test.receptfria');
    Route::get('/test_kiosk', 'ProductController@test_kiosk')->name('product.test.kiosk');

    Route::get('/product/all', 'ProductController@index')->name('product.index');
    Route::get('/product/availability', 'ProductController@checkProductAvailabilty')->name('check.product.availability');
    Route::post('/product/check/availability', 'ProductController@checkProductAvailabilty')->name('check.product.availability.now');
    Route::post('/products/destroy', 'ProductController@destroy')->name('product.destroy');
    Route::get('earnings', 'EarningController@index')->name('earning.index');
    Route::get('earnings/date', 'EarningController@date')->name('earning.date');

    Route::get('earnings/delivery', 'EarningController@delivery')->name('earning.delivery');
    Route::get('earnings/saas', 'EarningController@saas')->name('earning.saas');

    Route::get('importexcelfile', 'ProductController@importExcelView')->name('import.excel');
    Route::post('importexceldata', 'ProductController@importExcelData')->name('import.excel.data');

});
