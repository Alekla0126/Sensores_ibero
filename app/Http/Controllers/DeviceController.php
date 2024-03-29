<?php

    namespace App\Http\Controllers;

    use App\Models\DeviceState;
    use Illuminate\Http\Request;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;

    class DeviceController extends Controller
    {
        public function save(Request $request)
        {
            // The encryption key is set.
            // Uncomment the following line if you want to use an encrypted channel with a HASH algorithm.
            // Crypt::setKey('huberboy');
            // The token is received and decrypted.
            // Uncomment the following line if you want to encrypt the channel with a HASH algorithm.
            // $request->token = Crypt::decrypt($request->token);
            if (DeviceState::where('token', '=', $request->token)->first())
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
                    // The duplicated devices are eliminated.
                    $devices = $devices->unique('device_id')->values()->all();
                    // The websocket events are triggered.
                    event(new \App\Events\TableUpdater($devices));
                    event(new \App\Events\TemperatureUpdater($new));
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
        }

        public function update(Request $request)
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
        }

        public function delete(Request $request)
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
        }

        public function add(Request $request)
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
        }
    }
