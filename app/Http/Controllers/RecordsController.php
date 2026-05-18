<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Goal;
use App\Models\MatchModel;
use App\Models\PlayerAppearance;
use App\Models\Squad;
use App\Models\Tournament;
use Illuminate\View\View;

class RecordsController extends Controller
{
    public function index(): View
    {
        $gender = $this->gender();
        $tids = Tournament::forGender($gender)->pluck('tournament_id');

        $allTimeScorers = Goal::where('own_goal', false)
            ->whereIn('tournament_id', $tids)
            ->selectRaw('player_id, count(*) as total_goals')
            ->groupBy('player_id')
            ->orderByDesc('total_goals')
            ->limit(20)
            ->with('player.team')
            ->get();

        $mostTournaments = Squad::whereIn('tournament_id', $tids)
            ->selectRaw('player_id, count(distinct tournament_id) as tournaments')
            ->groupBy('player_id')
            ->orderByDesc('tournaments')
            ->limit(20)
            ->with('player.team')
            ->get();

        $mostMatches = PlayerAppearance::whereIn('tournament_id', $tids)
            ->selectRaw('player_id, count(*) as matches')
            ->groupBy('player_id')
            ->orderByDesc('matches')
            ->limit(20)
            ->with('player.team')
            ->get();

        $goldenBoots = Award::whereIn('tournament_id', $tids)
            ->where(fn ($q) => $q->where('award_name', 'like', '%Golden Boot%')
                ->orWhere('award_name', 'like', '%Top Scorer%')
                ->orWhere('award_name', 'like', '%Golden Shoe%'))
            ->with(['tournament', 'player.team'])
            ->orderByDesc('tournament_id')
            ->get();

        $goldenBalls = Award::whereIn('tournament_id', $tids)
            ->where('award_name', 'like', '%Golden Ball%')
            ->with(['tournament', 'player.team'])
            ->orderByDesc('tournament_id')
            ->get();

        $biggestWins = MatchModel::whereIn('tournament_id', $tids)
            ->selectRaw('*, abs(home_score - away_score) as margin')
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->with(['homeTeam', 'awayTeam', 'tournament'])
            ->orderByDesc('margin')
            ->limit(10)
            ->get();

        return view('records.index', compact(
            'allTimeScorers', 'mostTournaments', 'mostMatches',
            'goldenBoots', 'goldenBalls', 'biggestWins'
        ));
    }
}
