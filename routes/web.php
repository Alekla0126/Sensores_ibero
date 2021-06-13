<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'DashboardController@index')
        ->name('dashboard');
});

Route::get('/devices/create', function (\App\Repositories\Devices $devices) {
    $devices->save([
        'uuid' => '1234',
        'name' => 'test device 1',
        'type' => 'readable',
        'active' => 1
    ]);
});

Route::get('/devices/update', function (\App\Repositories\Devices $devices) {
    $devices->withPipeGroup('vip')->save([
        'active' => 0
    ], \App\Repositories\Devices::find(14));
});

Route::get('/devices/delete', function (\App\Repositories\Devices $devices) {
    $devices->delete(\App\Models\Device::find(15));
});
