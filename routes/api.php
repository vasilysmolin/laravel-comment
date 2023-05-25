<?php


use App\Http\Controllers\AdController;
use App\Http\Controllers\My\MyAdController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'App\Http\Controllers\Auth',
    'prefix' => 'auth',
], function () {

    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user', 'AuthController@user')
        ->middleware('auth:api');

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('ads', AdController::class)
    ->middleware('auth:api');

Route::group([
    'prefix' => 'my',
], function () {
    Route::apiResource('ads', MyAdController::class, [
        'names' => [
            'index' => 'my.ads.index',
            'store' => 'my.ads.store',
            'update' => 'my.ads.update',
            'destroy' => 'my.ads.destroy',
            'show' => 'my.ads.show',
        ],
    ])
        ->middleware('auth:api');
});


