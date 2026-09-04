<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConductorSession extends Model
{
    protected $fillable = [
        'bus_id',
        'conductor_id',
        'started_at',
        'ended_at',
        'end_reason'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function conductor()
    {
        return $this->belongsTo(Conductor::class);
    }
}
