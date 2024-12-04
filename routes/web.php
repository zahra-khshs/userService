<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/verify-email/{token}', 'Auth\VerificationController@verify')->name('verify.email');
