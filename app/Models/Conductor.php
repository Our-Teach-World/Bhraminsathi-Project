<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    protected $fillable = [
        'name',
        'phone'
    ];

    public function sessions()
    {
        return $this->hasMany(ConductorSession::class);
    }
}
