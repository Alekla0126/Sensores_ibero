<?php

    use App\Models\DeviceState;
    use Illuminate\Http\Request;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;
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

    Auth::ROUTES(['register' => FALSE]);

    /* Here are the routes that need authentication.
    */
    Route::GROUP(['middleware' => 'auth'], function ()
    {
        Route::GET('/', 'TableController@index')->name('table');
        Route::GET('graph/{device_id}', 'DashboardController@index');
    });

    /* In order to add a device, you should call this function of the
     * API.
    */
    Route::GET('/devices/add', function (Request $request)
    {
        // New instance of device
        $device = new DeviceState;
        // The id of the device is captured.
        $device->device_id = $request->device_id;
        // The value of the device is captured.
        $device->value = $request->value;
        // The token value is captured.
        $device->token = $request->token;
        // The instance was saved in the database.
        $result = $device->save();
        // If everything worked correctly, the info will be return in
        // the next lines.
        if ($result)
        {
            $query = DeviceState::all();
            broadcast(new \App\Events\TableUpdater($query));
            broadcast(new \App\Events\TemperatureUpdater($device->value));
            return response([$request], 200);
        }
        // If the saving failed, the user will be notified.
        else
        {
            return ['result' => 'Fallo la creación del dispositivo'];
        }
    });

    /*
     * Function for updating the device states.
     * Parameters: Request, a JSON variable with the device information
     * for the POST method.
     */
    Route::POST('/devices/save', 'DeviceController@save')->name('save');


    Route::PUT('/devices/update', function (Request $request)
    {
        $device = DeviceState::find($request->id);
        // Id.
        $device->id = $request->id;
        // Value
        $device->value = $request->value;
        // Time is captured.
        $device->timestamps = Carbon::now()->format('Y-m-d H:i:s');
        // Token record is added.
        $device->token = $request->token;
        // The new data is saved.
        $device = $device->save();
        if ($device)
        {
            // All the info is extracted.
            $query = DeviceState::all();
            // An event in the websockets channel is broadcasted.
            broadcast(new \App\Events\TableUpdater($query));
            broadcast(new \App\Events\TemperatureUpdater($device));
            // The response is returned.
            return response([$request], 200);
        }
        else
        {
            // If any inconvenient, the user is notified about it.
            return ['result' => 'Fallo la actualización de la temperatura'];
        }
    });

    Route::GET('/devices/delete', function (Request $request)
    {
        // The record is searched.
        $devices_rec = DeviceState::find($request->id);
        // The record is deleted.
        $devices_rec->delete($devices_rec);
        // If any problem, we notified the user. Otherwise, the info is
        // broadcasted, and send to the user with a success code.
        if ($devices_rec)
        {
            broadcast(new \App\Events\TableUpdater($devices_rec));
            broadcast(new \App\Events\TemperatureUpdater($new));
            return response([$devices_rec], 200);
        }
        else
        {
            return ['result' => 'Fallo la eliminación de la captura del dispositivo'];
        }
    });

    Auth::ROUTES();

    Route::GET('/all', ['uses' => 'TableController@renderAll'])->name('rerender');

    Route::GET('/home', [
        App\Http\Controllers\HomeController::class,
        'index'
    ])->name('home');
