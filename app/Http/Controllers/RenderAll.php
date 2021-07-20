<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;

class RenderAll extends Controller
{
    /**
     * @return Renderable
     */
    public function index(): Renderable
    {
        $devices = DB::table('device_states');
        return view('records')->with([
            'devices' => $devices
        ]);
    }
}
