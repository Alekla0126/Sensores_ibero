<?php

    namespace App\Http\Controllers;


    use App\Models\DashboardBlock;
    use App\Models\DeviceState;


    class DashboardController extends Controller
    {
        public function index($id)
        {
            $blocks = DashboardBlock::query()->orderBy('order')->get();
            $device = DeviceState::where('id', $id)->first();
            return view('dashboard')->with([
                'blocks',
                $blocks,
                'device' => $device
            ]);
        }
    }
