<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $teamId = session('active_team_id');
        Log::info('Active team ID: ' . $teamId);

        // Totaal aantal trainingen
        $totalTrainings = Event::where('team_id', $teamId)
            ->where('type', 'Training')
            ->count();

        // Opkomst per speler
        $attendanceStats = Attendance::with('player')
            ->whereHas('event', fn($q) => $q->where('team_id', $teamId)->where('type', 'Training'))
            ->get()
            ->groupBy('player_id')
            ->map(function ($attendances) use ($totalTrainings) {
                $present = $attendances->where('status', 'Aanwezig')->count();
                $late = $attendances->where('status', 'Aanwezig')->where('late', true)->count();
                $absent = $attendances->where('status', 'Afwezig')->count();

                return [
                    'player_id' => $attendances->first()->player->id,
                    'first_name' => $attendances->first()->player->first_name,
                    'last_name'  => $attendances->first()->player->last_name,
                    'present' => $present,
                    'late' => $late,
                    'absent' => $absent,
                    'percentage' => $totalTrainings > 0 ? round(($present / $totalTrainings) * 100) : 0,
                ];
            })
            ->values();

        return Inertia::render('Dashboard', [
            'attendanceStats' => $attendanceStats,
            'totalTrainings' => $totalTrainings,
        ]);
    }

    public function setActiveTeam(Request $request)
    {
        $validated = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $allowed = $request->user()
            ->teams()
            ->whereKey($validated['team_id'])
            ->exists();

        abort_unless($allowed, 403, 'Je mag dit team niet selecteren.');

        session(['active_team_id' => $validated['team_id']]);

        return redirect()->route('dashboard');
    }

    public function settings()
    {
        return Inertia::render('Settings');
    }
}
