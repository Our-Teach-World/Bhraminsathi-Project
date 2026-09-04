<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $fillable = [
        'bus_number',
        'route_id',
        'current_lat',
        'current_lng',
        'status',
        'last_updated_at'
    ];

    protected $casts = [
        'last_updated_at' => 'datetime'
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function sessions()
    {
        return $this->hasMany(ConductorSession::class);
    }

    public function errorFlags()
    {
        return $this->hasMany(BusErrorFlag::class);
    }
}
