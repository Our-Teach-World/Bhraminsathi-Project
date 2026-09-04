<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Conductor;
use App\Models\ConductorSession;

class ConductorController extends Controller
{
    /**
     * Render Conductor shift dashboard view
     */
    public function dashboard()
    {
        return view('conductor.dashboard');
    }

    /**
     * Register or update conductor profile in database
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20'
        ]);

        $conductor = Conductor::updateOrCreate(
            ['phone' => $request->phone],
            ['name' => $request->name]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Conductor details stored in database successfully.',
            'conductor' => [
                'id' => $conductor->id,
                'name' => $conductor->name,
                'phone' => $conductor->phone,
            ]
        ]);
    }

    /**
     * Start a conductor tracking session for a bus
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'bus_id' => 'required'
        ]);

        // Find Bus by code or ID
        $bus = Bus::where('bus_code', $request->bus_id)->first() 
            ?? Bus::find(is_numeric($request->bus_id) ? $request->bus_id : 1);

        if (!$bus) {
            return response()->json(['status' => 'error', 'message' => 'Bus vehicle not found'], 404);
        }

        // Get or Create Conductor in Database
        $conductor = null;
        if (!empty($request->conductor_id) && is_numeric($request->conductor_id)) {
            $conductor = Conductor::find($request->conductor_id);
        }

        if (!$conductor && !empty($request->phone)) {
            $conductor = Conductor::where('phone', $request->phone)->first();
        }

        if (!$conductor) {
            $conductorName = $request->conductor_name ?? 'Rajesh Sharma';
            $conductorPhone = $request->conductor_phone ?? $request->phone ?? '9876543210';

            $conductor = Conductor::updateOrCreate(
                ['phone' => $conductorPhone],
                ['name' => $conductorName]
            );
        }

        $session = ConductorSession::create([
            'bus_id' => $bus->id,
            'conductor_id' => $conductor->id,
            'started_at' => now(),
            'end_reason' => 'none'
        ]);

        // Update Bus Status to Live
        $bus->update([
            'status' => 'live',
            'last_updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Shift session started successfully.',
            'session_id' => $session->id,
            'conductor_id' => $conductor->id
        ]);
    }

    /**
     * Broadcast live GPS coordinate from conductor phone
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'bus_id' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric'
        ]);

        $bus = Bus::where('bus_code', $request->bus_id)->first() 
            ?? Bus::find(is_numeric($request->bus_id) ? $request->bus_id : 1);

        if ($bus) {
            $bus->update([
                'current_lat' => $request->lat,
                'current_lng' => $request->lng,
                'last_updated_at' => now()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Location updated in database.'
        ]);
    }

    /**
     * Stop conductor shift session
     */
    public function stopSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'reason' => 'required|in:manual,geofence_auto'
        ]);

        $session = ConductorSession::find($request->session_id);
        if ($session) {
            $session->update([
                'ended_at' => now(),
                'end_reason' => $request->reason
            ]);

            $bus = Bus::find($session->bus_id);
            if ($bus) {
                // If stopped by geofence auto-end at change point, flag error until new session
                $status = ($request->reason === 'geofence_auto') ? 'error' : 'no_session';
                $bus->update([
                    'status' => $status
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shift session ended.'
        ]);
    }
}
