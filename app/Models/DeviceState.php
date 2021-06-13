<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DeviceState
 *
 * @package App\Models
 */
class DeviceState extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device(): BelongsTo
    {
       return $this->belongsTo(Device::class, 'device_id');
    }
}
