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
            // Gebruik ends_at als die er is, anders starts_at
            $query->where(function($q) use ($now) {
                $q->where(function($subQ) use ($now) {
                    $subQ->whereNotNull('ends_at')
                         ->where('ends_at', '>=', $now);
                })->orWhere(function($subQ) use ($now) {
                    $subQ->whereNull('ends_at')
                         ->where('starts_at', '>=', $now);
                });
            });
        } elseif ($filter === 'past') {
            // Gebruik ends_at als die er is, anders starts_at
            $query->where(function($q) use ($now) {
                $q->where(function($subQ) use ($now) {
                    $subQ->whereNotNull('ends_at')
                         ->where('ends_at', '<', $now);
                })->orWhere(function($subQ) use ($now) {
                    $subQ->whereNull('ends_at')
                         ->where('starts_at', '<', $now);
                });
            });
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

        return redirect()
            ->route('games.index')
            ->with('success', 'Wedstrijd succesvol aangemaakt!');
    }

    public function edit(Event $event)
    {
        // Blokkeer bewerken van verlopen evenementen (kijk naar eindtijd)
        $checkTime = $event->ends_at ?? $event->starts_at;
        if ($checkTime < now()) {
            return redirect()
                ->route('games.index')
                ->with('error', 'Verlopen wedstrijden kunnen niet meer bewerkt worden.');
        }
        
        $event->load('game');

        return Inertia::render('Games/Form', [
            'event' => $event,
            'defaults' => [
                'team_id'   => $event->team_id,
                'starts_at' => $event->starts_at,
                'ends_at'   => $event->ends_at,
                'location'  => $event->location,
                'opponent'  => $event->game->opponent,
                'home'      => $event->game->home,
                'notes'     => $event->notes,
            ],
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'starts_at' => ['required', 'date'],
            'ends_at'   => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location'  => ['nullable', 'string', 'max:255'],
            'opponent'  => ['required', 'string', 'max:255'],
            'home'      => ['required', 'boolean'],
            'notes'     => ['nullable', 'string'],
        ]);

        // Update het Event
        $event->update([
            'starts_at' => $data['starts_at'],
            'ends_at'   => $data['ends_at'],
            'location'  => $data['location'],
            'notes'     => $data['notes'],
        ]);

        // Update de game-specifieke info
        $event->game->update([
            'opponent' => $data['opponent'],
            'home'     => $data['home'],
        ]);

        return redirect()
            ->route('games.index')
            ->with('success', 'Wedstrijd succesvol bijgewerkt!');
    }

    public function evaluate(Event $event)
    {
        $event->load(['game.playerStats.player', 'team.players']);

        $game = $event->game;

        // Zet goals/assists opnieuw in een array die de frontend verwacht
        $goals = [];
        foreach ($game->playerStats as $stat) {
            for ($i = 0; $i < $stat->goals; $i++) {
                $goals[] = [
                    'scorer' => $stat->player_id,
                    'assist' => null, // assist wordt apart gezet
                ];
            }
            for ($i = 0; $i < $stat->assists; $i++) {
                $goals[] = [
                    'scorer' => null,
                    'assist' => $stat->player_id,
                ];
            }
        }

        // Kaarten array zoals je formulier het verwacht
        $cards = [];
        foreach ($event->team->players as $player) {
            $stat = $game->playerStats->firstWhere('player_id', $player->id);
            $cards[$player->id] = [
                'yellow' => $stat?->yellow_cards ?? 0,
                'red'    => $stat?->red_cards ?? 0,
            ];
        }

        return Inertia::render('Games/Evaluate', [
            'event'     => $event,
            'game'      => $game,
            'players'   => $event->team->players,
            'stats'     => $game->playerStats->keyBy('player_id'),
            'defaults'  => [
                'our_score'      => $game->our_score ?? 0,
                'opponent_score' => $game->opponent_score ?? 0,
                'goals'          => $goals,
                'cards'          => $cards,
            ],
        ]);
    }


    public function storeEvaluation(Request $request, Event $event)
    {
        $data = $request->validate([
            'our_score'       => ['required', 'integer', 'min:0'],
            'opponent_score'  => ['required', 'integer', 'min:0'],
            'goals'           => ['array'],
            'goals.*.scorer'  => ['nullable', 'exists:players,id'],
            'goals.*.assist'  => ['nullable', 'exists:players,id'],
            'cards'           => ['array'],
            'cards.*.yellow'  => ['integer', 'min:0'],
            'cards.*.red'     => ['integer', 'min:0'],
        ]);

        Log::info('Evaluatie data', $data);

        $game = $event->game;

        // ✅ Update de score in de game
        $game->update([
            'our_score'      => $data['our_score'],
            'opponent_score' => $data['opponent_score'],
        ]);

        // ✅ Reset oude stats voor dit game
        \App\Models\GamePlayerStats::where('game_id', $game->id)->delete();

        // ✅ Doelpunten en assists
        foreach ($data['goals'] ?? [] as $goal) {
            if (!empty($goal['scorer'])) {
                \App\Models\GamePlayerStats::create([
                    'game_id'   => $game->id,
                    'player_id' => $goal['scorer'],
                    'goals'     => 1,
                    'assists'   => !empty($goal['assist']) ? 1 : 0,
                ]);

                if (!empty($goal['assist']) && $goal['assist'] !== $goal['scorer']) {
                    \App\Models\GamePlayerStats::create([
                        'game_id'   => $game->id,
                        'player_id' => $goal['assist'],
                        'assists'   => 1,
                    ]);
                }
            }
        }

        // ✅ Kaarten
        foreach ($data['cards'] ?? [] as $playerId => $cards) {
            if (($cards['yellow'] ?? 0) > 0 || ($cards['red'] ?? 0) > 0) {
                \App\Models\GamePlayerStats::updateOrCreate(
                    ['game_id' => $game->id, 'player_id' => $playerId],
                    [
                        'yellow_cards' => $cards['yellow'] ?? 0,
                        'red_cards'    => $cards['red'] ?? 0,
                    ]
                );
            }
        }

        return redirect()
            ->route('games.index')
            ->with('success', 'Wedstrijdstatistieken opgeslagen.');
    }
}
