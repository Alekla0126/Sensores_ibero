<?php

    namespace App\Http\Controllers;

    use App\Models\DashboardBlock;
    use App\Models\DeviceState;
    use Illuminate\Contracts\Support\Renderable;

    class DashboardController extends Controller
    {
        /**
         * @return Renderable
         */
        public function index(): Renderable
        {
            $blocks = DashboardBlock::query()->orderBy('order')->get();
            $device = DeviceState::where('device_id', 1)->first();
            return view('dashboard')->with([
                'blocks',
                $blocks,
                'value' => $device->value
            ]);
        }
    }
