<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\ManagerAppointment;
use App\Models\MatchModel;
use App\Models\QualifiedTeam;
use App\Models\Squad;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(): View
    {
        $tournaments = Tournament::forGender($this->gender())->with('winner')->orderByDesc('year')->get();

        return view('tournaments.index', compact('tournaments'));
    }

    public function show(int $year): View
    {
        $tournament = Tournament::where('year', $year)
            ->forGender($this->gender())
            ->with(['winner', 'runnerUp', 'thirdPlace', 'awards.player', 'awards.team'])
            ->firstOrFail();

        $groups = QualifiedTeam::where('tournament_id', $tournament->tournament_id)
            ->with('team')
            ->whereNotNull('group_name')
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->get()
            ->groupBy('group_name')
            ->sortKeys();

        $topScorers = Goal::where('tournament_id', $tournament->tournament_id)
            ->where('own_goal', false)
            ->selectRaw('player_id, team_id, count(*) as goals')
            ->groupBy('player_id', 'team_id')
            ->orderByDesc('goals')
            ->limit(20)
            ->with(['player', 'team'])
            ->get();

        $matches = MatchModel::where('tournament_id', $tournament->tournament_id)
            ->with(['homeTeam', 'awayTeam', 'goals.player'])
            ->orderBy('match_date')
            ->orderBy('match_id')
            ->get()
            ->groupBy('stage_name');

        $squadTeams = Squad::where('tournament_id', $tournament->tournament_id)
            ->with('team')
            ->get()
            ->pluck('team')
            ->filter()
            ->unique('team_id')
            ->sortBy('team_name')
            ->values();

        return view('tournaments.show', compact(
            'tournament', 'groups', 'topScorers', 'matches', 'squadTeams'
        ));
    }

    public function teamSquad(int $year, string $teamId): View
    {
        $tournament = Tournament::where('year', $year)->forGender($this->gender())->firstOrFail();
        $team = Team::findOrFail($teamId);

        $squad = Squad::where('tournament_id', $tournament->tournament_id)
            ->where('team_id', $teamId)
            ->with('player')
            ->orderByRaw("CASE position_code WHEN 'GK' THEN 1 WHEN 'DF' THEN 2 WHEN 'MF' THEN 3 WHEN 'FW' THEN 4 ELSE 5 END")
            ->orderBy('shirt_number')
            ->get();

        $manager = ManagerAppointment::where('tournament_id', $tournament->tournament_id)
            ->where('team_id', $teamId)
            ->with('manager')
            ->first();

        return view('tournaments.squad', compact('tournament', 'team', 'squad', 'manager'));
    }
}
