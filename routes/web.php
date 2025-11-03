<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PracticeController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/instellingen', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/instellingen', [DashboardController::class, 'updateSettings'])->name('settings.update');

    // Active team
    Route::post('/active-team', [DashboardController::class, 'setActiveTeam'])
        ->name('active-team.set');

    // Players
    Route::get('/spelers', [PlayerController::class, 'index'])->name('players.index');
    Route::get('/spelers/nieuw', [PlayerController::class, 'create'])->name('players.create');
    Route::post('/spelers', [PlayerController::class, 'store'])->name('players.store');
    Route::get('/spelers/{player}/bewerken', [PlayerController::class, 'edit'])->name('players.edit');
    Route::put('/spelers/{player}', [PlayerController::class, 'update'])->name('players.update');
    Route::get('/spelers/{player}/stats', [PlayerController::class, 'show'])->name('players.show');
    Route::delete('/spelers/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

    // Practices
    Route::get('/trainingen', [PracticeController::class, 'index'])->name('practices.index');
    Route::get('/trainingen/nieuw', [PracticeController::class, 'create'])->name('practices.create');
    Route::post('/trainingen', [PracticeController::class, 'store'])->name('practices.store');

    // Matches
    Route::get('/wedstrijden', [GameController::class, 'index'])->name('games.index');
    Route::get('/wedstrijden/nieuw', [GameController::class, 'create'])->name('games.create');
    Route::post('/wedstrijden', [GameController::class, 'store'])->name('games.store');
    Route::get('/wedstrijden/{event}/evalueren', [GameController::class, 'evaluate'])->name('games.evaluate');
    Route::put('/wedstrijden/{event}/evalueren', [GameController::class, 'storeEvaluation'])->name('games.storeEvaluation');

    // Attendance
    Route::prefix('events/{event}')->group(function () {
        Route::get('/aanwezigheid', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::post('/aanwezigheid', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::put('/aanwezigheid', [AttendanceController::class, 'update'])->name('attendance.update');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes van Breeze
require __DIR__ . '/auth.php';
