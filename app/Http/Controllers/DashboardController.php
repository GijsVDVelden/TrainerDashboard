<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Enums\EventType;
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
            ->where('type', EventType::Training->value)
            ->count();

        // All events for calendar (last 30 days and next 60 days to cover current month view)
        $calendarEvents = Event::where('team_id', $teamId)
            ->where('starts_at', '>=', now()->subDays(30))
            ->where('starts_at', '<=', now()->addDays(60))
            ->orderBy('starts_at')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'type' => $event->type,
                    'starts_at' => $event->starts_at,
                    'ends_at' => $event->ends_at,
                    'location' => $event->location,
                    'game' => $event->game ? [
                        'opponent' => $event->game->opponent,
                        'home' => $event->game->home,
                    ] : null,
                ];
            });

        // Upcoming events for sidebar (only future events, next 30 days)
        $upcomingEvents = Event::where('team_id', $teamId)
            ->where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addDays(30))
            ->orderBy('starts_at')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'type' => $event->type,
                    'starts_at' => $event->starts_at,
                    'ends_at' => $event->ends_at,
                    'location' => $event->location,
                    'game' => $event->game ? [
                        'opponent' => $event->game->opponent,
                        'home' => $event->game->home,
                    ] : null,
                ];
            });

        // Recent events (last 5)
        $recentEvents = Event::where('team_id', $teamId)
            ->where('starts_at', '<', now())
            ->orderBy('starts_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'type' => $event->type,
                    'starts_at' => $event->starts_at,
                    'location' => $event->location,
                    'game' => $event->game ? [
                        'opponent' => $event->game->opponent,
                        'home' => $event->game->home,
                    ] : null,
                ];
            });

        // Player stats
        $totalPlayers = \App\Models\Player::where('team_id', $teamId)->count();

        // Game stats
        $totalGames = Event::where('team_id', $teamId)
            ->where('type', EventType::Match->value)
            ->count();

        $upcomingGames = Event::where('team_id', $teamId)
            ->where('type', EventType::Match->value)
            ->where('starts_at', '>=', now())
            ->count();

        // Opkomst per speler
        $attendanceStats = Attendance::with('player')
            ->whereHas('event', fn($q) => $q->where('team_id', $teamId)->where('type', EventType::Training->value))
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
            'calendarEvents' => $calendarEvents,
            'upcomingEvents' => $upcomingEvents,
            'recentEvents' => $recentEvents,
            'totalPlayers' => $totalPlayers,
            'totalGames' => $totalGames,
            'upcomingGames' => $upcomingGames,
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

    public function storeSettings(Request $request)
    {
        // Validatie en opslaan van instellingen
    }
}
