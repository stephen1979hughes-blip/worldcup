<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/gender/{gender}', function (string $gender) {
    $gender = in_array($gender, ['men', 'women']) ? $gender : 'men';
    session(['gender' => $gender]);
    return redirect(url()->previous(route('home')));
})->name('gender.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('tournaments')->name('tournaments.')->group(function () {
    Route::get('/', [TournamentController::class, 'index'])->name('index');
    Route::get('/{year}', [TournamentController::class, 'show'])->name('show')->where('year', '[0-9]{4}');
    Route::get('/{year}/squads/{teamId}', [TournamentController::class, 'teamSquad'])->name('squad')->where('year', '[0-9]{4}');
});

Route::prefix('players')->name('players.')->group(function () {
    Route::get('/', [PlayerController::class, 'index'])->name('index');
    Route::get('/{playerId}', [PlayerController::class, 'show'])->name('show');
});

Route::prefix('teams')->name('teams.')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('index');
    Route::get('/{teamId}', [TeamController::class, 'show'])->name('show');
});

Route::get('/matches', [\App\Http\Controllers\MatchController::class, 'index'])->name('matches.index');
Route::get('/matches/{matchId}', [\App\Http\Controllers\MatchController::class, 'show'])->name('matches.show');

Route::get('/records', [RecordsController::class, 'index'])->name('records');
Route::get('/search', [PlayerController::class, 'search'])->name('search');
