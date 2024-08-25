
<?php

use Illuminate\Support\Facades\Route;

Route::group( ['namespace' => 'Addons\Category\Controllers'], function () {
    Route::get( '/category', 'CategoryController@index' );
    Route::get( '/category/create', 'CategoryController@create' );
} );
