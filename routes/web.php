<?php

    use App\Models\DeviceState;
    use Illuminate\Http\Request;
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

    Auth::routes(['register' => FALSE]);

    Route::group(['middleware' => 'auth'], function ()
    {
        Route::get('/', 'DashboardController@index')->name('dashboard');
    });

    Route::get('add', function (Request $request)
    {
        $device = new DeviceState();
        $device->device_id = $request->device_id;
        $device->value = $request->value;
        $result = $device->save();
        if ($result)
        {
            return ['result' => 'Se creo con exito el su dispositivo'];
        }
        else
        {
            return ['result' => 'Fallo la cración del dispositivo'];
        }
    });

    Route::put('update', function (Request $request)
    {
        $device = DeviceState::find($request->device_id);
        $device->value = $request->value;
        $result = $device->save();
        if ($result)
        {
            $value = $device->value;
            event(new \App\Events\TemperatureUpdater('hola mundo'));
            return ['result' => 'Se actualizo la temperatura'];
        }
        else
        {
            return ['result' => 'Fallo la actualización de la temperatura'];
        }
    });

    Route::get('/devices/create', function (\App\Repositories\Devices $devices)
    {
        $devices->save([
            'uuid' => '1234',
            'name' => 'test device 1',
            'type' => 'readable',
            'active' => 1
        ]);
    });

    Route::get('/devices/update', function (\App\Repositories\Devices $devices)
    {
        $devices->withPipeGroup('vip')->save([
            'active' => 0
        ], \App\Repositories\Devices::find(14));
    });

    Route::get('/devices/delete', function (\App\Repositories\Devices $devices)
    {
        $devices->delete(\App\Models\Device::find(15));
    });
