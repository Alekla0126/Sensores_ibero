<?php

    namespace App\Http\Controllers;


    use App\Models\DashboardBlock;
    use App\Models\DeviceState;


    class DashboardController extends Controller
    {
        public function index($device_id)
        {
            $blocks = DashboardBlock::query()->orderBy('order')->get();
            $device = DeviceState::where('device_id', $device_id)->latest('updated_at')->first();
            return view('dashboard')->with([
                'blocks',
                $blocks,
                'device' => $device
            ]);
        }
    }
