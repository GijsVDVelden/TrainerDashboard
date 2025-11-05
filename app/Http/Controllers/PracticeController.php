<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
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

        $event = Event::create([
            'team_id'   => $teamId,
            'type'      => \App\Enums\EventType::Training,
            'starts_at' => $data['starts_at'],
            'ends_at'   => $data['ends_at'] ?? null,
            'location'  => $data['location'] ?? null,
            'notes'     => $data['notes'] ?? null,
        ]);

        return redirect()->route('practices.index')->with('success', 'Training aangemaakt.');
    }

    public function edit(Event $event)
    {
        // Blokkeer bewerken van verlopen evenementen (kijk naar eindtijd)
        $checkTime = $event->ends_at ?? $event->starts_at;
        if ($checkTime < now()) {
            return redirect()
                ->route('practices.index')
                ->with('error', 'Verlopen trainingen kunnen niet meer bewerkt worden.');
        }
        
        return Inertia::render('Practices/Form', [
            'event' => $event,
            'defaults' => [
                'team_id'   => $event->team_id,
                'starts_at' => $event->starts_at,
                'ends_at'   => $event->ends_at,
                'location'  => $event->location,
                'notes'     => $event->notes,
            ],
        ]);
    }

    public function update(PracticeRequest $request, Event $event)
    {
        $data = $request->validated();

        $event->update([
            'starts_at' => $data['starts_at'],
            'ends_at'   => $data['ends_at'] ?? null,
            'location'  => $data['location'] ?? null,
            'notes'     => $data['notes'] ?? null,
        ]);

        return redirect()->route('practices.index')->with('success', 'Training bijgewerkt.');
    }

}
