<?php

namespace App\Listeners;

use App\Events\TempretureUpdater;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTemperatureUpdater
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TempretureUpdater  $event
     * @return void
     */
    public function handle(TempretureUpdater $event)
    {
        //
    }
}
