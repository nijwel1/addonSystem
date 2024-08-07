
<?php

use Illuminate\Support\Facades\Route;

Route::group( ['namespace' => 'Addons\Blog\Controllers'], function () {
    Route::get( '/blog', 'BlogController@index' );
    Route::get( '/blog/create', 'BlogController@create' );
} );
