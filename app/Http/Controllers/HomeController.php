<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\QualifiedTeam;
use App\Models\Squad;
use App\Models\Tournament;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $gender = $this->gender();

        $tournaments = Tournament::forGender($gender)->with('winner')->orderByDesc('year')->get();

        $tournamentIds = $tournaments->pluck('tournament_id');

        $stats = [
            'tournaments' => $tournaments->count(),
            'goals'       => Goal::whereIn('tournament_id', $tournamentIds)->where('own_goal', false)->count(),
            'players'     => Squad::whereIn('tournament_id', $tournamentIds)->distinct('player_id')->count('player_id'),
            'teams'       => QualifiedTeam::whereIn('tournament_id', $tournamentIds)->distinct('team_id')->count('team_id'),
        ];

        return view('home', compact('tournaments', 'stats'));
    }
}
