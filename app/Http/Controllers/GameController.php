<?php

namespace App\Http\Controllers;

use App\Enums\EventType;
use App\Models\Event;
use Illuminate\Http\Request;
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
            ->where('team_id', $teamId);

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
}
