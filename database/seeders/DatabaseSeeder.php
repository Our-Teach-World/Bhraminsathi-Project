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
        // 1. Seed Jaipur Routes
        $r12 = Route::create([
            'name' => 'Jaipur Junction ↔ Sanganer Airport',
            'start_point_lat' => 26.9200,
            'start_point_lng' => 75.7877,
            'end_point_lat' => 26.8285,
            'end_point_lng' => 75.8056,
            'change_point_lat' => 26.8380,
            'change_point_lng' => 75.7950,
            'stops_json' => ['Jaipur Junction', 'MI Road', 'Rambagh Circle', 'Tonk Phatak', 'B2 Bypass', 'Sanganer Airport']
        ]);

        $r42 = Route::create([
            'name' => 'Sindhi Camp ISBT ↔ Sitapura Ind. Area',
            'start_point_lat' => 26.9248,
            'start_point_lng' => 75.7980,
            'end_point_lat' => 26.7820,
            'end_point_lng' => 75.8230,
            'change_point_lat' => 26.8380,
            'change_point_lng' => 75.7950,
            'stops_json' => ['Sindhi Camp ISBT', 'Ajmer Gate', 'SMS Hospital', 'Tonk Phatak', 'B2 Bypass', 'Sitapura Ind. Area']
        ]);

        // 2. Seed Conductors
        $c1 = Conductor::create(['name' => 'Ramesh K.', 'phone' => '+91 98765 43210']);
        $c2 = Conductor::create(['name' => 'Suresh M.', 'phone' => '+91 98765 11223']);

        // 3. Seed Jaipur Bus Fleet
        Bus::create([
            'bus_number' => 'RJ-14-PA-1234',
            'route_id' => $r12->id,
            'current_lat' => 26.8380,
            'current_lng' => 75.7950,
            'status' => 'live',
            'last_updated_at' => now()
        ]);

        Bus::create([
            'bus_number' => 'RJ-14-PA-4242',
            'route_id' => $r42->id,
            'current_lat' => 26.7850,
            'current_lng' => 75.8200,
            'status' => 'live',
            'last_updated_at' => now()
        ]);

        Bus::create([
            'bus_number' => 'RJ-14-PA-0808',
            'route_id' => $r42->id,
            'current_lat' => 26.8380,
            'current_lng' => 75.7950,
            'status' => 'error',
            'last_updated_at' => now()->subMinutes(8)
        ]);
    }
}
