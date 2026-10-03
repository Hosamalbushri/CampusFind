<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api', 'middleware' => ['api']], function () {
    // Web API routes
});
