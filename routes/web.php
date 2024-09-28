<?php

use App\Http\Controllers\AddonController;
use Illuminate\Support\Facades\Route;

Route::get( '/', function () {
    return view( 'welcome' );
} );

// routes/web.php
Route::get( 'addons/upload', [AddonController::class, 'showUploadForm'] )->name( 'addons.upload' );
Route::post( 'addons/upload', [AddonController::class, 'uploadAddon'] )->name( 'addons.upload.post' );


// ------ dinislam route -----
Route::prefix('dinislam')->group(function () {
    Route::get('/', [DinislamController::class, 'index'])->name('dinislam.index');
    Route::post('/store', [DinislamController::class, 'store'])->name('dinislam.store');
    Route::get('/edit/{id}', [DinislamController::class, 'edit'])->name('dinislam.edit');
    Route::post('/update/{id}', [DinislamController::class, 'update'])->name('dinislam.update');
    Route::delete('/delete/{id}', [DinislamController::class, 'destroy'])->name('dinislam.destroy');
});


// ------ dinislam route -----
Route::prefix('dinislam')->group(function () {
    Route::get('/', [DinislamController::class, 'index'])->name('dinislam.index');
    Route::post('/store', [DinislamController::class, 'store'])->name('dinislam.store');
    Route::get('/edit/{id}', [DinislamController::class, 'edit'])->name('dinislam.edit');
    Route::post('/update/{id}', [DinislamController::class, 'update'])->name('dinislam.update');
    Route::delete('/delete/{id}', [DinislamController::class, 'destroy'])->name('dinislam.destroy');
});
