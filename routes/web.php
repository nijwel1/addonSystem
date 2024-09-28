<?php

use App\Http\Controllers\AddonController;
use Illuminate\Support\Facades\Route;

Route::get( '/', function () {
    return view( 'welcome' );
} );

// routes/web.php
Route::get( 'addons/upload', [AddonController::class, 'showUploadForm'] )->name( 'addons.upload' );
Route::post( 'addons/upload', [AddonController::class, 'uploadAddon'] )->name( 'addons.upload.post' );

Route::middleware( 'auth' )->group( function () {
    // ------ dinislam route -----
    Route::prefix( 'dinislam' )->group( function () {
        Route::get( '/', [DinislamController::class, 'index'] )->name( 'dinislam.index' );
        Route::post( '/store', [DinislamController::class, 'store'] )->name( 'dinislam.store' );
        Route::get( '/edit/{id}', [DinislamController::class, 'edit'] )->name( 'dinislam.edit' );
        Route::post( '/update/{id}', [DinislamController::class, 'update'] )->name( 'dinislam.update' );
        Route::delete( '/delete/{id}', [DinislamController::class, 'destroy'] )->name( 'dinislam.destroy' );
    } );

// ------ caregory route -----
    Route::prefix( 'caregory' )->group( function () {
        Route::get( '/', [CaregoryController::class, 'index'] )->name( 'caregory.index' );
        Route::post( '/store', [CaregoryController::class, 'store'] )->name( 'caregory.store' );
        Route::get( '/edit/{id}', [CaregoryController::class, 'edit'] )->name( 'caregory.edit' );
        Route::post( '/update/{id}', [CaregoryController::class, 'update'] )->name( 'caregory.update' );
        Route::delete( '/delete/{id}', [CaregoryController::class, 'destroy'] )->name( 'caregory.destroy' );
    } );

// ------ caregory route -----
    Route::prefix( 'caregory' )->group( function () {
        Route::get( '/', [CaregoryController::class, 'index'] )->name( 'caregory.index' );
        Route::post( '/store', [CaregoryController::class, 'store'] )->name( 'caregory.store' );
        Route::get( '/edit/{id}', [CaregoryController::class, 'edit'] )->name( 'caregory.edit' );
        Route::post( '/update/{id}', [CaregoryController::class, 'update'] )->name( 'caregory.update' );
        Route::delete( '/delete/{id}', [CaregoryController::class, 'destroy'] )->name( 'caregory.destroy' );
    } );

// ------ caregory route -----
    Route::prefix( 'caregory' )->group( function () {
        Route::get( '/', [CaregoryController::class, 'index'] )->name( 'caregory.index' );
        Route::post( '/store', [CaregoryController::class, 'store'] )->name( 'caregory.store' );
        Route::get( '/edit/{id}', [CaregoryController::class, 'edit'] )->name( 'caregory.edit' );
        Route::post( '/update/{id}', [CaregoryController::class, 'update'] )->name( 'caregory.update' );
        Route::delete( '/delete/{id}', [CaregoryController::class, 'destroy'] )->name( 'caregory.destroy' );
    } );

    

// ------ category route -----
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/update/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
});

// ------ route end -----
} );
