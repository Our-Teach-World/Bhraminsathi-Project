<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusErrorFlag extends Model
{
    protected $fillable = [
        'bus_id',
        'flagged_at',
        'reminder_count',
        'resolved_at',
        'resolved_by'
    ];

    protected $casts = [
        'flagged_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
