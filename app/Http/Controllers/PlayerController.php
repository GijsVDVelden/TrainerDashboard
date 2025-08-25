<?php

namespace App\Http\Controllers;

use App\Enums\PlayerPositions;
use App\Http\Requests\PlayerRequest;
use App\Models\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PlayerController extends Controller
{
    public function index(): Response
    {
        $activeTeamId = session('active_team_id');

        $players = Player::with('team')
            ->where('team_id', $activeTeamId)
            ->orderBy('last_name')
            ->get();

        return Inertia::render('Players/Index', [
            'players' => $players,
            // Handig voor forms/filters aan clientzijde
            'positions' => collect(PlayerPositions::cases())->map(fn($c) => [
                'key' => $c->name,
                'label' => $c->value,
            ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Players/Form', [
            'positions' => collect(PlayerPositions::cases())->map(fn($c) => [
                'key' => $c->value,
                'label' => $c->value,
            ]),
            'player' => null,
        ]);
    }

    public function store(PlayerRequest $request): RedirectResponse
    {
        $activeTeamId = session('active_team_id');

        Player::create([
            'team_id'    => $activeTeamId,
            'first_name' => $request->input('first_name'),
            'last_name'  => $request->input('last_name'),
            // Door de cast kun je de enum of string opslaan:
            'position'   => PlayerPositions::from($request->input('position')),
            'birth_date' => $request->input('birth_date'),
        ]);

        return redirect()->route('players.index')->with('success', 'Speler aangemaakt.');
    }


    public function edit(Player $player)
    {
        abort_unless($player->team_id === session('active_team_id'), 403);

        return Inertia::render('Players/Form', [
            'positions' => collect(PlayerPositions::cases())->map(fn($c) => [
                'key'   => $c->value,
                'label' => $c->value,
            ]),
            'player' => [
                'id'         => $player->id,
                'first_name' => $player->first_name,
                'last_name'  => $player->last_name,
                'position'   => $player->position->value, // DB waarde -> dropdown value
                'birth_date' => optional($player->birth_date)->format('Y-m-d'),
            ],
        ]);
    }

    public function update(PlayerRequest $request, Player $player): RedirectResponse
    {
        abort_unless($player->team_id === session('active_team_id'), 403);

        $player->update([
            'first_name' => $request->input('first_name'),
            'last_name'  => $request->input('last_name'),
            'position'   => PlayerPositions::from($request->input('position')),
            'birth_date' => $request->input('birth_date'),
        ]);

        return redirect()->route('players.index')->with('success', 'Speler bijgewerkt.');
    }

    public function show(Player $player)
    {
        abort_unless($player->team_id === session('active_team_id'), 403);

        $player->load([
            'team',
            'attendances.event',
        ]);

        return Inertia::render('Players/Show', [
            'player'      => $player,
            'stats'       => $player->aggregatedStats(),
            'attendances' => $player->attendances,
        ]);
    }

    public function destroy(Player $player): RedirectResponse
    {
        abort_unless($player->team_id === session('active_team_id'), 403);

        $player->delete();

        return redirect()->route('players.index')->with('success', 'Speler verwijderd.');
    }
}
