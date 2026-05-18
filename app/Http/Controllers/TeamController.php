<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\QualifiedTeam;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::withCount(['qualifiedTournaments as appearances'])
            ->orderByDesc('appearances')
            ->orderBy('team_name')
            ->get();

        $teams = $teams->map(function ($team) {
            $team->titles_count = Tournament::where('winner_team_id', $team->team_id)->count();
            return $team;
        });

        return view('teams.index', compact('teams'));
    }

    public function show(string $teamId): View
    {
        $team = Team::findOrFail($teamId);
        $gender = $this->gender();
        $tids = Tournament::forGender($gender)->pluck('tournament_id');

        $history = QualifiedTeam::where('team_id', $teamId)
            ->whereIn('tournament_id', $tids)
            ->with('tournament')
            ->orderByDesc('tournament_id')
            ->get();

        $topScorers = Goal::where('team_id', $teamId)
            ->where('own_goal', false)
            ->whereIn('tournament_id', $tids)
            ->selectRaw('player_id, count(*) as goals')
            ->groupBy('player_id')
            ->orderByDesc('goals')
            ->limit(10)
            ->with('player')
            ->get();

        $titles = Tournament::forGender($gender)
            ->where('winner_team_id', $teamId)
            ->orderByDesc('year')
            ->get();

        $stats = [
            'appearances' => $history->count(),
            'titles'      => $titles->count(),
            'goals'       => Goal::where('team_id', $teamId)->where('own_goal', false)->whereIn('tournament_id', $tids)->count(),
        ];

        return view('teams.show', compact('team', 'history', 'topScorers', 'titles', 'stats'));
    }
}
