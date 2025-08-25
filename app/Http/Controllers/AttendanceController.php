<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function edit(Event $event)
    {
        $players = $event->team
            ? $event->team->players()->orderBy('last_name')->get()->values()
            : collect();

        $attendances = $event->attendances()->get()->keyBy('player_id');

        return Inertia::render('Attendances/Form', [
            'event' => $event,
            'players' => $players,
            'attendances' => $attendances,
            'statuses' => [
                AttendanceStatus::PRESENT->value,
                AttendanceStatus::ABSENT->value,
                AttendanceStatus::LATE->value,
            ],
        ]);
    }
    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.player_id' => ['required', 'exists:players,id'],
            'attendances.*.status' => ['required', new Enum(AttendanceStatus::class)],
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
                    'notes' => $attendanceData['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('practices.index', $event)
            ->with('success', 'Aanwezigheid opgeslagen.');
    }
}
