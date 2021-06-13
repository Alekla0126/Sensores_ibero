<?php

namespace App\Http\Controllers;

use App\Models\DashboardBlock;
use Illuminate\Contracts\Support\Renderable;

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): Renderable
    {
        $blocks = DashboardBlock::query()->orderBy('order')->get();

        return view('dashboard')
            ->with('blocks', $blocks);
    }
}
