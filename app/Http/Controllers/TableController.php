<?php

    namespace App\Http\Controllers;

    use App\Models\DeviceState;
    use Illuminate\Contracts\Support\Renderable;


    class TableController extends Controller
    {
        /**
         * @return Renderable
         */
        public function index(): Renderable
        {
            $devices = DeviceState::all();
            return view('table')->with([
                'devices' => $devices
            ]);
        }
    }
