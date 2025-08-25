<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\EventType;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $teamId = session('active_team_id');

        if (!$teamId) {
            return Inertia::render('Games/Index', [
                'events' => [],
                'filter' => $request->query('filter', 'upcoming'),
            ]);
        }

        $filter = $request->query('filter', 'upcoming');
        $now = now();

        $query = Event::query()
            ->where('type', EventType::Match)
            ->where('team_id', $teamId)
            ->with('game'); // << hier game-relatie laden

        if ($filter === 'upcoming') {
            $query->where('starts_at', '>=', $now->startOfDay());
        } elseif ($filter === 'past') {
            $query->where('starts_at', '<', $now->startOfDay());
        }

        $events = $query
            ->orderByDesc('starts_at')
            ->get();

        return Inertia::render('Games/Index', [
            'events' => $events,
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {
        $teamId = session('active_team_id');

        return Inertia::render('Games/Form', [
            'defaults' => [
                'team_id'   => $teamId,
                'starts_at' => null,
                'ends_at'   => null,
                'location'  => null,
                'opponent'  => null,
                'home'      => true,
                'notes'     => null,
            ],
        ]);
    }

    public function store(Request $request)
    {
        Log::info($request->all());

        $teamId = session('active_team_id');

        $data = $request->validate([
            'starts_at' => ['required', 'date'],
            'ends_at'   => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location'  => ['nullable', 'string', 'max:255'],
            'opponent'  => ['required', 'string', 'max:255'],
            'home'      => ['required', 'boolean'],
            'notes'     => ['nullable', 'string'],
        ]);

        // Stap 1: maak het Event aan
        $event = Event::create([
            'team_id'   => $teamId,
            'type'      => EventType::Match,
            'starts_at' => $data['starts_at'],
            'ends_at'   => $data['ends_at'],
            'location'  => $data['location'],
            'notes'     => $data['notes'],
        ]);

        // Stap 2: koppel de wedstrijdspecifieke info
        $event->game()->create([
            'opponent' => $data['opponent'],
            'home'     => $data['home'],
        ]);

        $players = $event->team->players;

        foreach ($players as $player) {
            $event->attendances()->create([
                'player_id' => $player->id,
                'status'    => AttendanceStatus::PRESENT,
                'reason'    => null,
                'late'      => false,
                'notes'     => null,
            ]);
        }

        return redirect()
            ->route('games.index')
            ->with('success', 'Wedstrijd succesvol aangemaakt!');
    }

    public function evaluate(Event $event)
    {
        $event->load(['game.playerStats.player']);

        return Inertia::render('Games/Evaluate', [
            'event' => $event,
            'game'  => $event->game,
            'players' => $event->team->players,
            'stats'   => $event->game->playerStats->keyBy('player_id'),
        ]);
    }

}
