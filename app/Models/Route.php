<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'name',
        'start_point_lat',
        'start_point_lng',
        'end_point_lat',
        'end_point_lng',
        'change_point_lat',
        'change_point_lng',
        'stops_json'
    ];

    protected $casts = [
        'stops_json' => 'array'
    ];

    public function buses()
    {
        return $this->hasMany(Bus::class);
    }
}
