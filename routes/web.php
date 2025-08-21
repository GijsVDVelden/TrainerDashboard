<?php

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Stuur de root altijd naar het dashboard (auth middleware vangt niet-ingelogde users af)
Route::get('/', fn () => redirect()->route('dashboard'));

// Alles waarvoor je ingelogd moet zijn
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
        ->name('dashboard');

    Route::post('/active-team', function (Request $request) {
        $validated = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
        ]);

        $allowed = $request->user()
            ->teams()
            ->whereKey($validated['team_id'])
            ->exists();

        abort_unless($allowed, 403, 'Je mag dit team niet selecteren.');

        session(['active_team_id' => $validated['team_id']]);

        return back();
    })->name('active-team.set');

    Route::get('/players', [PlayerController::class, 'index'])->name('players.index');

    // Profiel
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes van Breeze
require __DIR__ . '/auth.php';
