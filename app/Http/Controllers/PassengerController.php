<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;

class PassengerController extends Controller
{
    /**
     * Render main Passenger search & live map view
     */
    public function index()
    {
        return view('passenger.index');
    }

    /**
     * API endpoint to poll nearby live bus positions
     */
    public function getNearbyBuses(Request $request)
    {
        $routeId = $request->query('route_id');

        $query = Bus::with('route');
        if ($routeId && $routeId !== 'all') {
            $query->where('route_id', $routeId);
        }

        $buses = $query->get()->map(function ($bus) {
            return [
                'id' => 'BUS-' . $bus->id,
                'bus_number' => $bus->bus_number,
                'route_name' => $bus->route ? $bus->route->name : 'Local Route',
                'status' => $bus->status,
                'lat' => $bus->current_lat,
                'lng' => $bus->current_lng,
                'last_updated' => $bus->last_updated_at ? $bus->last_updated_at->diffForHumans() : 'Never'
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $buses
        ]);
    }
}
