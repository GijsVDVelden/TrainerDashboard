<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('team')
            ->where('team_id', session('active_team_id'))
            ->get();

        return Inertia::render('Players/Index', [
            'players' => $players,
        ]);
    }
}
