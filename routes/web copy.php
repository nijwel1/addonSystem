<?php

use App\Http\Controllers\AddonController;
use Illuminate\Support\Facades\Route;

Route::get( '/', function () {
    return view( 'welcome' );
} );

// routes/web.php
Route::get( 'addons/upload', [AddonController::class, 'showUploadForm'] )->name( 'addons.upload' );
Route::post( 'addons/upload', [AddonController::class, 'uploadAddon'] )->name( 'addons.upload.post' );
