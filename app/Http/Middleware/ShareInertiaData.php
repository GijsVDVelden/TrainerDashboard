<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareInertiaData
{
    public function handle(Request $request, Closure $next)
    {
        $activeTeamId = session('active_team_id');

        Inertia::share([
            'auth' => [
                'user' => $request->user(),
                'userTeams' => $request->user()?->teams()->with('season')->get() ?? [],
                'activeTeam' => $activeTeamId
                    ? $request->user()?->teams()->with('season')->find($activeTeamId)
                    : null,
            ]
        ]);

        return $next($request);
    }
}
