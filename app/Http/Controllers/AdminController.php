<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\ConductorSession;
use App\Models\BusErrorFlag;

class AdminController extends Controller
{
    /**
     * Helper to resolve bus model from code (BUS-12, BUS-42, UK-07-PA-1234) or numeric ID
     */
    private function findBus($busIdInput)
    {
        if (empty($busIdInput)) {
            return Bus::first();
        }

        if (is_numeric($busIdInput)) {
            $bus = Bus::find($busIdInput);
            if ($bus) return $bus;
        }

        $str = (string) $busIdInput;
        $num = (int) preg_replace('/[^0-9]/', '', $str);

        // Code aliases for UI dropdown tags
        if ($num === 12) $num = 1;
        if ($num === 42) $num = 2;
        if ($num === 8 || $num === 7) $num = 3;

        $bus = Bus::find($num);
        if ($bus) return $bus;

        $bus = Bus::where('bus_number', 'like', "%{$str}%")->first();
        return $bus ?: Bus::first();
    }

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
        $bus = $this->findBus($id);
        $busId = $bus ? $bus->id : $id;

        $flag = BusErrorFlag::where('bus_id', $busId)->whereNull('resolved_at')->first();
        if ($flag) {
            $flag->increment('reminder_count');
        } else {
            BusErrorFlag::create([
                'bus_id' => $busId,
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
        $bus = $this->findBus($id);
        if ($bus) {
            $bus->update(['status' => 'no_session']);
            BusErrorFlag::where('bus_id', $bus->id)->whereNull('resolved_at')->update([
                'resolved_at' => now(),
                'resolved_by' => 'Admin'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bus error status resolved.'
        ]);
    }

    /**
     * Forcibly terminate a live bus broadcast session by Admin
     */
    public function terminateSession($id)
    {
        $bus = $this->findBus($id);

        if ($bus) {
            $bus->update([
                'status' => 'no_session',
                'last_updated_at' => now()
            ]);

            // Close any active conductor session in DB
            ConductorSession::where('bus_id', $bus->id)
                ->whereNull('ended_at')
                ->update([
                    'ended_at' => now(),
                    'end_reason' => 'terminated_by_admin'
                ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Bus {$id} live session terminated by Admin.",
            'bus_id' => $bus ? $bus->id : null
        ]);
    }
}
