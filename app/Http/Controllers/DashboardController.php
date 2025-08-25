<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard');
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
