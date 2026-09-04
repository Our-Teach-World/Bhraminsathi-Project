<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Route;
use App\Models\Bus;
use App\Models\Conductor;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Routes
        $r12 = Route::create([
            'name' => 'City Centre ↔ Airport',
            'start_point_lat' => 30.3165,
            'start_point_lng' => 78.0322,
            'end_point_lat' => 30.3340,
            'end_point_lng' => 78.0510,
            'change_point_lat' => 30.3240,
            'change_point_lng' => 78.0415,
            'stops_json' => ['City Centre', 'Railway Station', 'Ghanta Ghar', 'Airport']
        ]);

        $r42 = Route::create([
            'name' => 'ISBT ↔ Rajpur Road',
            'start_point_lat' => 30.2850,
            'start_point_lng' => 78.0050,
            'end_point_lat' => 30.3650,
            'end_point_lng' => 78.0800,
            'change_point_lat' => 30.3240,
            'change_point_lng' => 78.0415,
            'stops_json' => ['ISBT', 'Ghanta Ghar', 'Rajpur Road']
        ]);

        // 2. Seed Conductors
        $c1 = Conductor::create(['name' => 'Ramesh K.', 'phone' => '+91 98765 43210']);
        $c2 = Conductor::create(['name' => 'Suresh M.', 'phone' => '+91 98765 11223']);

        // 3. Seed Buses
        Bus::create([
            'bus_number' => 'UK-07-PA-1234',
            'route_id' => $r12->id,
            'current_lat' => 30.3200,
            'current_lng' => 78.0370,
            'status' => 'live',
            'last_updated_at' => now()
        ]);

        Bus::create([
            'bus_number' => 'UK-07-PA-4242',
            'route_id' => $r42->id,
            'current_lat' => 30.3300,
            'current_lng' => 78.0480,
            'status' => 'live',
            'last_updated_at' => now()
        ]);

        Bus::create([
            'bus_number' => 'UK-07-PA-0808',
            'route_id' => $r42->id,
            'current_lat' => 30.3240,
            'current_lng' => 78.0415,
            'status' => 'error',
            'last_updated_at' => now()->subMinutes(8)
        ]);
    }
}
