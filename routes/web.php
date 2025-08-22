<?php

use App\Http\Controllers\EventController;
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

    // Players
    Route::get('/spelers', [PlayerController::class, 'index'])->name('players.index');
    Route::get('/spelers/create', [PlayerController::class, 'create'])->name('players.create');
    Route::post('/spelers', [PlayerController::class, 'store'])->name('players.store');
    Route::get('/spelers/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit');
    Route::put('/spelers/{player}', [PlayerController::class, 'update'])->name('players.update');
    Route::delete('/spelers/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

    // Events
    Route::get('/trainingen', [EventController::class, 'practicesIndex'])
        ->name('practices.index');
    Route::get('/trainingen/create', [EventController::class, 'practicesCreate'])->name('practices.create');
    Route::post('/trainingen', [EventController::class, 'practicesStore'])->name('practices.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes van Breeze
require __DIR__ . '/auth.php';
