<?php

    namespace App\Http\Controllers;

    use Illuminate\Contracts\Support\Renderable;
    use Illuminate\Support\Facades\DB;


    class TableController extends Controller
    {

        // This functions returns a view with the last record of each device.
        /**
         * @return Renderable
         */
        public function index(): Renderable
        {
            $devices = DB::table('device_states')
                ->groupBy('id', 'device_id', 'value', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc')
                ->get();
            $devices = $devices->unique('device_id')->values()->all();
            return view('table')->with([
                'devices' => $devices
            ]);
        }

        // This functions returns a view with al the registries.
        public function renderAll()
        {
            $devices = DB::table('device_states')->get();
            return view('table')->with([
                'devices' => $devices
            ]);
        }

        // This functions returns a view with the last record of each device. Note: this
        // differ from the index, because its called independently.
        public function actualRender()
        {
            $devices = DB::table('device_states')
                ->groupBy('id', 'device_id', 'value', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc')
                ->get();
            $devices = $devices->unique('device_id')->values()->all();
            return view('table')->with([
                'devices' => $devices
            ]);
        }

    }
