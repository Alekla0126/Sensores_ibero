<?php

    namespace App\Http\Controllers;


    use App\Models\DashboardBlock;
    use App\Models\DeviceState;
    use Illuminate\Contracts\View\View;


    class DashboardController extends Controller
    {
        private $id;

        public function __construct($id)
        {
            $this->id = $id;
        }

        public function index($id)
        {
            $blocks = DashboardBlock::query()->orderBy('order')->get();
            $device = DeviceState::where('device_id', $this->id)->first();
            return view('dashboard')->with([
                'blocks',
                $blocks,
                'value' => $device->value
            ]);
        }
    }
