<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareInertiaData
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $teams = $user
            ? $user->teams()->with('season')->orderBy('id')->get()
            : collect();

        // Haal huidige keuze uit de sessie
        $activeTeamId = session('active_team_id');

        // Als nog niets gekozen is maar user heeft teams: pak de eerste
        if (!$activeTeamId && $teams->isNotEmpty()) {
            $activeTeamId = $teams->first()->id;
            session(['active_team_id' => $activeTeamId]);
        }

        // Zoek het actieve team in de al opgehaalde lijst
        $activeTeam = $activeTeamId
            ? $teams->firstWhere('id', $activeTeamId)
            : null;

        Inertia::share([
            'auth' => [
                'user'       => $user,
                'userTeams'  => $teams,
                'activeTeam' => $activeTeam,
            ],
        ]);

        return $next($request);
    }
}
