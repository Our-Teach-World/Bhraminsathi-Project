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

        $query = Bus::with(['route', 'sessions' => function($q) {
            $q->whereNull('ended_at')->with('conductor');
        }]);

        if ($routeId && $routeId !== 'all') {
            $query->where('route_id', $routeId);
        }

        $buses = $query->get()->map(function ($bus) {
            $routeName = $bus->route ? $bus->route->name : 'Local City Route';
            $parts = explode('↔', $routeName);
            $heading = isset($parts[1]) ? trim($parts[1]) : $routeName;

            $activeSession = $bus->sessions->first();
            $conductorName = ($activeSession && $activeSession->conductor) ? $activeSession->conductor->name : null;
            $conductorId = ($activeSession && $activeSession->conductor) ? $activeSession->conductor->id : null;

            return [
                'id' => 'BUS-' . sprintf("%02d", $bus->id),
                'numeric_id' => $bus->id,
                'bus_number' => $bus->bus_number,
                'route_name' => $routeName,
                'route_id' => $bus->route_id ?: 12,
                'heading' => $heading,
                'status' => $bus->status,
                'lat' => (float) $bus->current_lat,
                'lng' => (float) $bus->current_lng,
                'active_conductor_name' => $conductorName,
                'active_conductor_id' => $conductorId,
                'last_updated' => $bus->last_updated_at ? $bus->last_updated_at->diffForHumans() : 'Just now'
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $buses
        ]);
    }
}
