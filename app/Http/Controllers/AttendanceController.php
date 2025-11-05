<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceReason;
use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function edit(Request $request, Event $event)
    {
        $players = $event->team
            ? $event->team->players()->orderBy('last_name')->get()->values()
            : collect();

        // Maak automatisch attendances aan voor alle spelers als ze nog niet bestaan
        foreach ($players as $player) {
            Attendance::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'player_id' => $player->id,
                ],
                [
                    'status' => AttendanceStatus::PRESENT,
                    'reason' => null,
                    'late' => false,
                    'notes' => null,
                ]
            );
        }

        $attendances = $event->attendances()->get()->keyBy('player_id');

        return Inertia::render('Attendances/Form', [
            'event' => [
                'id' => $event->id,
                'type' => $event->type->value,
                'title' => $event->game?->opponent ?? 'Training',
                'starts_at' => $event->starts_at,
                'ends_at' => $event->ends_at,
            ],
            'players' => $players,
            'attendances' => $attendances,
            'statuses' => collect(AttendanceStatus::cases())->map(fn($c) => $c->value),
            'reasons'  => collect(AttendanceReason::cases())->map(fn($c) => $c->value),
            'from' => $request->query('from'),
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.player_id' => ['required', 'exists:players,id'],
            'attendances.*.status' => ['required', new Enum(AttendanceStatus::class)],
            'attendances.*.reason' => ['nullable', new Enum(AttendanceReason::class)],
            'attendances.*.late' => ['sometimes', 'boolean'],
            'attendances.*.notes' => ['nullable', 'string'],
        ]);

        foreach ($data['attendances'] as $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'player_id' => $attendanceData['player_id'],
                ],
                [
                    'status' => $attendanceData['status'],
                    'reason' => $attendanceData['reason'] ?? null,
                    'late'   => $attendanceData['late'] ?? false,
                    'notes'  => $attendanceData['notes'] ?? null,
                ]
            );
        }

        if ($event->type === \App\Enums\EventType::Training) {
            return redirect()->route('practices.index')
                ->with('success', 'Aanwezigheid opgeslagen.');
        }

        if ($event->type === \App\Enums\EventType::Match) {
            return redirect()->route('games.index')
                ->with('success', 'Aanwezigheid opgeslagen.');
        }

        // fallback
        return redirect()->back()->with('success', 'Aanwezigheid opgeslagen.');
    }
}
