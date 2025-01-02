<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UrlController;


Route::prefix('1')->group(function() {

    Route::controller(UrlController::class)->group(function () {
        Route::post('/encode', 'encodeUrl')->name('api.encode');
        Route::get('/decode', 'decodeUrl')->name('api.decode');
    });

});
