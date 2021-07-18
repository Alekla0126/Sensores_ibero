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
        Route::get('/', 'TableController@index')->name('table');
        Route::get('graph/{device_id}', 'DashboardController@index');
    });

    /* In order to add a device, you should call this function of the
     * API.
    */
    Route::GET('/devices/add', function (Request $request)
    {
        $device = new DeviceState;
        $device->device_id = $request->device_id;
        $device->value = $request->value;
        $result = $device->save();
        if ($result)
        {
            $query = DeviceState::all();
            broadcast(new \App\Events\TableUpdater($query));
            broadcast(new \App\Events\TemperatureUpdater($device->value));
            return response([$request->all()], 200);
        }
        else
        {
            return ['result' => 'Fallo la cración del dispositivo'];
        }
    });

    /*
     * Function for updating the device states.
     * Parameters: Request, a JSON variable with the device information
     * for the POST method.
     */
    Route::POST('/devices/save', function (Request $request)
    {
        if(DeviceState::where('token', '=', $request->token)->first())
        {
            // $device = DeviceState::find($request->id);
            // A new device state is instantiated.
            $new = new DeviceState();
            // The string with the classroom id is captured.
            $new->device_id = $request->device_id;
            // The value of the request is captured.
            $new->value = $request->value;
            // The time is captured.
            $new->timestamps = Carbon::now()->format('Y-m-d H:i:s');
            // The token record is added.
            $new->token = $request->token;
            // The new data is saved.
            $new->save();
            // If the object is added to the database we return the data in a
            // JSON.
            if ($new)
            {
                // The devices are ordered by creation date.
                $devices = DB::table('device_states')->orderBy('created_at', 'desc')->get();
                // The duplicates devices are eliminated.
                $devices = $devices->unique('device_id')->values()->all();
                // The websocket events are triggered.
                broadcast(new \App\Events\TableUpdater($devices));
                broadcast(new \App\Events\TemperatureUpdater($new));
                // The function returns the array and the 'OK' response.
                return response([$new], 200);
            }
            // In case that an error occurred.
            else
            {
                return ['result' => 'Fallo la actualización de la temperatura'];
            }
        }
        // In case that the given token is invalid.
        else
        {
            return ['result' => 'Fallo la actualización de la temperatura, el token no es valido'];
        }
    });

    Route::PUT('/devices/update', function (Request $request)
    {
        $device = DeviceState::find($request->id);
        // Id.
        $device->id = $request->id;
        // Valor.
        $device->value = $request->value;
        // Se guarda.
        $device = $device->save();
        if ($device)
        {
            // Se trae toda la información de los aparatos.
            $query = DeviceState::all();
            // Se crea un evento en los canales del Websocket.
            broadcast(new \App\Events\TableUpdater($query));
            broadcast(new \App\Events\TemperatureUpdater($device));
            // Se regresa la respuesta del servidor.
            return response([$request->all()], 200);
        }
        else
        {
            return ['result' => 'Fallo la actualización de la temperatura'];
        }
    });

    Route::GET('/devices/delete', function (Request $request)
    {
        $devices = DeviceState::find($request->id);
        $devices->delete($devices);
    });

    Auth::ROUTES();

    Route::GET('/home', [
        App\Http\Controllers\HomeController::class,
        'index'
    ])->name('home');
