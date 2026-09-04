<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\ConductorSession;
use App\Models\BusErrorFlag;

class AdminController extends Controller
{
    /**
     * Render Admin Overview Live Dashboard
     */
    public function overview()
    {
        return view('admin.overview');
    }

    /**
     * Render Bus Detail & Resolution Page
     */
    public function busDetail($id)
    {
        return view('admin.bus-detail', ['busId' => $id]);
    }

    /**
     * Render Historical Session Logs Page
     */
    public function sessionLogs()
    {
        return view('admin.session-log');
    }

    /**
     * Trigger SMS/Nudge reminder to conductor
     */
    public function sendReminder($id)
    {
        $flag = BusErrorFlag::where('bus_id', $id)->whereNull('resolved_at')->first();
        if ($flag) {
            $flag->increment('reminder_count');
        } else {
            BusErrorFlag::create([
                'bus_id' => $id,
                'flagged_at' => now(),
                'reminder_count' => 1
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Automated reminder dispatched to conductor phone.'
        ]);
    }

    /**
     * Manually resolve bus error state
     */
    public function resolveError($id)
    {
        $bus = Bus::find($id);
        if ($bus) {
            $bus->update(['status' => 'no_session']);
        }

        BusErrorFlag::where('bus_id', $id)->whereNull('resolved_at')->update([
            'resolved_at' => now(),
            'resolved_by' => 'Admin'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Bus error status resolved.'
        ]);
    }
}
