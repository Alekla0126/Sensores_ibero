<?php

namespace App\Repositories;

use App\Models\Device;
use RoyVoetman\Repositories\Repository;

class Devices extends Repository
{
    protected string $model = Device::class;
}
