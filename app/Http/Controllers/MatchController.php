<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Goal;
use App\Models\MatchModel;
use App\Models\PlayerAppearance;
use App\Models\Squad;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $gender = $this->gender();
        $tournaments = Tournament::forGender($gender)->orderByDesc('year')->get();
        $teams = Team::orderBy('team_name')->get();

        $query = MatchModel::with(['homeTeam', 'awayTeam', 'tournament'])
            ->whereHas('tournament', fn ($q) => $q->forGender($gender));

        if ($request->filled('year')) {
            $query->whereHas('tournament', fn ($q) => $q->where('year', $request->year));
        }

        if ($request->filled('team')) {
            $query->where(fn ($q) => $q
                ->where('home_team_id', $request->team)
                ->orWhere('away_team_id', $request->team)
            );
        }

        if ($request->filled('stage')) {
            $query->where('stage_name', $request->stage);
        }

        $matches = $query->orderByDesc('match_date')->orderBy('match_id')->paginate(30)->withQueryString();

        $stages = MatchModel::whereHas('tournament', fn ($q) => $q->forGender($gender))
            ->distinct()->orderBy('stage_name')->pluck('stage_name');

        return view('matches.index', compact('matches', 'tournaments', 'teams', 'stages'));
    }

    public function show(string $matchId): View
    {
        $match = MatchModel::with([
            'tournament',
            'homeTeam',
            'awayTeam',
            'stadium',
        ])->findOrFail($matchId);

        $goals = Goal::where('match_id', $matchId)
            ->with('player', 'team')
            ->orderBy('minute')
            ->orderBy('minute_stoppage')
            ->get();

        $bookings = Booking::where('match_id', $matchId)
            ->with('player', 'team')
            ->orderBy('minute')
            ->get();

        // Lineups from player_appearances; fall back to tournament squad if not available
        $homeAppearances = PlayerAppearance::where('match_id', $matchId)
            ->where('team_id', $match->home_team_id)
            ->with('player')
            ->get();

        $awayAppearances = PlayerAppearance::where('match_id', $matchId)
            ->where('team_id', $match->away_team_id)
            ->with('player')
            ->get();

        $lineupSource = 'appearances';

        if ($homeAppearances->isEmpty() && $awayAppearances->isEmpty()) {
            $lineupSource = 'squad';
            $homeAppearances = Squad::where('tournament_id', $match->tournament_id)
                ->where('team_id', $match->home_team_id)
                ->with('player')
                ->orderByRaw("CASE position_code WHEN 'GK' THEN 1 WHEN 'DF' THEN 2 WHEN 'MF' THEN 3 WHEN 'FW' THEN 4 ELSE 5 END")
                ->orderBy('shirt_number')
                ->get();

            $awayAppearances = Squad::where('tournament_id', $match->tournament_id)
                ->where('team_id', $match->away_team_id)
                ->with('player')
                ->orderByRaw("CASE position_code WHEN 'GK' THEN 1 WHEN 'DF' THEN 2 WHEN 'MF' THEN 3 WHEN 'FW' THEN 4 ELSE 5 END")
                ->orderBy('shirt_number')
                ->get();
        }

        // Split goals by team for timeline display
        $homeGoals = $goals->where('team_id', $match->home_team_id)->where('own_goal', false);
        $awayGoals = $goals->where('team_id', $match->away_team_id)->where('own_goal', false);

        // Own goals credited to the other team's score
        $homeOwnGoals = $goals->where('team_id', $match->away_team_id)->where('own_goal', true);
        $awayOwnGoals = $goals->where('team_id', $match->home_team_id)->where('own_goal', true);

        return view('matches.show', compact(
            'match', 'goals', 'bookings',
            'homeAppearances', 'awayAppearances', 'lineupSource',
            'homeGoals', 'awayGoals', 'homeOwnGoals', 'awayOwnGoals'
        ));
    }
}
