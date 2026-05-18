<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Goal;
use App\Models\Player;
use App\Models\Squad;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(Request $request): View
    {
        $gender = $this->gender();
        $genderTournamentIds = Tournament::forGender($gender)->pluck('tournament_id');

        $query = Player::with('team')
            ->whereHas('squads', fn ($q) => $q->whereIn('tournament_id', $genderTournamentIds))
            ->withCount([
                'goals as wc_goals' => fn ($q) => $q->where('own_goal', false)
                    ->whereIn('tournament_id', $genderTournamentIds),
            ])
            ->withCount(['squads as tournament_count' => fn ($q) => $q->whereIn('tournament_id', $genderTournamentIds)]);

        if ($request->filled('year')) {
            $tournamentId = Tournament::where('year', $request->year)->forGender($gender)->value('tournament_id');
            if ($tournamentId) {
                $query->whereHas('squads', fn ($q) => $q->where('tournament_id', $tournamentId));
            }
        }

        if ($request->filled('position')) {
            $query->whereHas('squads', fn ($q) => $q->where('position_code', $request->position)
                ->whereIn('tournament_id', $genderTournamentIds));
        }

        if ($request->filled('team')) {
            $query->where('team_id', $request->team);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('family_name', 'like', "%{$search}%")
                  ->orWhere('given_name', 'like', "%{$search}%");
            });
        }

        $players = $query->orderBy('family_name')->paginate(48)->withQueryString();

        $tournaments = Tournament::forGender($gender)->orderByDesc('year')->get(['year', 'host_country', 'tournament_id']);
        $teams = Team::orderBy('team_name')->get(['team_id', 'team_name', 'team_code']);

        return view('players.index', compact('players', 'tournaments', 'teams'));
    }

    public function show(string $playerId): View
    {
        $player = Player::with('team')->findOrFail($playerId);

        $appearances = Squad::where('player_id', $playerId)
            ->with('tournament.winner')
            ->orderBy('tournament_id')
            ->get();

        $goals = Goal::where('player_id', $playerId)
            ->where('own_goal', false)
            ->with(['match.homeTeam', 'match.awayTeam', 'tournament'])
            ->orderBy('tournament_id')
            ->orderBy('minute')
            ->get();

        $bookings = Booking::where('player_id', $playerId)
            ->with(['match.homeTeam', 'match.awayTeam', 'tournament'])
            ->orderBy('tournament_id')
            ->get();

        return view('players.show', compact('player', 'appearances', 'goals', 'bookings'));
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Player::with('team')
            ->where(function ($query) use ($q) {
                $query->where('family_name', 'like', "%{$q}%")
                      ->orWhere('given_name', 'like', "%{$q}%");
            })
            ->limit(8)
            ->get()
            ->map(fn ($p) => [
                'id'   => $p->player_id,
                'name' => $p->full_name,
                'team' => $p->team->team_name ?? '',
                'url'  => route('players.show', $p->player_id),
            ]);

        return response()->json($results);
    }
}
