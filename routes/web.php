<?php

use Illuminate\Support\Facades\Route;

Route::view('/{any?}', 'app')->where('any', '.*');

Route::get('/', function () {
    return view('welcome');
});
