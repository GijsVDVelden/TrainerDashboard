<?php
// app/Http/Controllers/EventController.php

namespace App\Http\Controllers;

use App\Http\Requests\PracticeRequest;
use App\Models\Event;
use App\Enums\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    public function practicesIndex(Request $request)
    {
        // Optioneel: filter op actief team als je dat hebt
        $teamId = optional($request->user())->active_team_id ?? null;

        $query = Event::query()
            ->where('type', EventType::Training);

        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        $events = $query
            ->orderByDesc('starts_at')
            ->paginate(25)
            ->through(function (Event $e) {
                return [
                    'id'        => $e->id,
                    'starts_at' => $e->starts_at,
                    'ends_at'   => $e->ends_at,
                    'location'  => $e->location,
                    'notes'     => Str::limit((string) $e->notes, 120),
                ];
            });

        return Inertia::render('Practices/Index', [
            'events' => $events,
        ]);
    }

    public function practicesCreate(Request $request)
    {
        // Stel team_id voor: actief team of kies uit lijst
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

    public function practicesStore(PracticeRequest $request)
    {
        Log::info('Incoming request data', $request->all());

        $data = $request->validated();

        // pak actief team uit de sessie als team_id niet meegestuurd is
        $teamId = $data['team_id'] ?? session('active_team_id');

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

        // Fix 3 — juiste route-naam gebruiken
        return redirect()->route('practices.index')->with('success', 'Training aangemaakt.');
    }

}
