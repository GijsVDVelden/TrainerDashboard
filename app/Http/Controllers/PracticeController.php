<?php

namespace App\Http\Controllers;

use App\Http\Requests\PracticeRequest;
use App\Models\Event;
use App\Enums\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PracticeController extends Controller
{
    public function index(Request $request)
    {
        $teamId = session('active_team_id');

        if (!$teamId) {
            return Inertia::render('Practices/Index', [
                'events' => [],
                'filter' => $request->query('filter', 'upcoming'),
            ]);
        }

        $filter = $request->query('filter', 'upcoming'); // standaard upcoming
        $now = now();

        $query = Event::query()
            ->where('type', EventType::Training)
            ->where('team_id', $teamId);

        if ($filter === 'upcoming') {
            $query->where('starts_at', '>=', $now->startOfDay());
        } elseif ($filter === 'past') {
            $query->where('starts_at', '<', $now->startOfDay());
        }

        $events = $query
            ->orderByDesc('starts_at')
            ->get(); // geen paginate()

        return Inertia::render('Practices/Index', [
            'events' => $events,
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {
        $activeTeamId = optional($request->user())->active_team_id;
        return Inertia::render('Practices/Form', [
            'defaults' => [
                'team_id'   => $activeTeamId,
                'starts_at' => null,
                'ends_at'   => null,
                'location'  => null,
                'notes'     => null,
            ],
        ]);
    }

    public function store(PracticeRequest $request)
    {

        $data = $request->validated();

        $teamId = session('active_team_id');

        if (!$teamId) {
            return back()->withErrors(['team_id' => 'Geen team geselecteerd of actief.']);
        }

        Event::create([
            'team_id'   => $teamId,
            'type'      => \App\Enums\EventType::Training,
            'starts_at' => $data['starts_at'],
            'ends_at'   => $data['ends_at'] ?? null,
            'location'  => $data['location'] ?? null,
            'notes'     => $data['notes'] ?? null,
        ]);

        return redirect()->route('practices.index')->with('success', 'Training aangemaakt.');
    }

}
