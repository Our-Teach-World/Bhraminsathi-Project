<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
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
     * Start a conductor tracking session for a bus
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'bus_id' => 'required',
            'conductor_id' => 'required'
        ]);

        $session = ConductorSession::create([
            'bus_id' => $request->bus_id,
            'conductor_id' => $request->conductor_id,
            'started_at' => now(),
            'end_reason' => 'none'
        ]);

        // Update Bus Status to Live
        $bus = Bus::find($request->bus_id);
        if ($bus) {
            $bus->update([
                'status' => 'live',
                'last_updated_at' => now()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shift session started successfully.',
            'session_id' => $session->id
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

        $bus = Bus::find($request->bus_id);
        if ($bus) {
            $bus->update([
                'current_lat' => $request->lat,
                'current_lng' => $request->lng,
                'last_updated_at' => now()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Location updated.'
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
