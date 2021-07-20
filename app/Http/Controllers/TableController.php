<?php

    namespace App\Http\Controllers;

    use Illuminate\Contracts\Support\Renderable;
    use Illuminate\Support\Facades\DB;


    class TableController extends Controller
    {

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

        public static function renderAll()
        {
            $devices = DB::table('device_states');
            return view('table')->with([
                'devices' => $devices
            ]);
        }
    }
