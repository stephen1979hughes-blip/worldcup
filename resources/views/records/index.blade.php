@extends('layouts.app')

@section('title', 'Records')
@section('meta_description', 'FIFA World Cup all-time records: top scorers, most appearances, Golden Boot winners, and biggest wins.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Records</h1>

    <div class="grid gap-8 lg:grid-cols-2">

        {{-- All-time top scorers --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-2">
                <i class="ti ti-ball-football text-green-600"></i>
                <h2 class="font-semibold text-gray-900">All-time Top Scorers</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-2.5 text-left w-8">#</th>
                        <th class="px-4 py-2.5 text-left">Player</th>
                        <th class="px-4 py-2.5 text-left">Team</th>
                        <th class="px-4 py-2.5 text-center">Goals</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($allTimeScorers as $i => $scorer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-2.5">
                            <a href="{{ route('players.show', $scorer->player_id) }}" class="font-medium hover:underline">
                                {{ $scorer->player?->full_name ?? $scorer->player_id }}
                            </a>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500">
                            @if($scorer->player?->team)
                            <a href="{{ route('teams.show', $scorer->player->team->team_id) }}" class="hover:underline">
                                {{ $scorer->player->team->flag_emoji }} {{ $scorer->player->team->team_code }}
                            </a>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-center font-bold text-green-600 text-base">{{ $scorer->total_goals }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Most tournament appearances --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-2">
                <i class="ti ti-calendar-repeat text-green-600"></i>
                <h2 class="font-semibold text-gray-900">Most Tournament Appearances</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-2.5 text-left w-8">#</th>
                        <th class="px-4 py-2.5 text-left">Player</th>
                        <th class="px-4 py-2.5 text-left">Team</th>
                        <th class="px-4 py-2.5 text-center">Tournaments</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($mostTournaments as $i => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-2.5">
                            <a href="{{ route('players.show', $row->player_id) }}" class="font-medium hover:underline">
                                {{ $row->player?->full_name ?? $row->player_id }}
                            </a>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500">
                            @if($row->player?->team)
                                {{ $row->player->team->flag_emoji }} {{ $row->player->team->team_code }}
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-center font-bold text-blue-600 text-base">{{ $row->tournaments }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Golden Boot winners --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-2">
                <i class="ti ti-shoe text-yellow-500"></i>
                <h2 class="font-semibold text-gray-900">Golden Boot Winners</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Year</th>
                        <th class="px-4 py-2.5 text-left">Award</th>
                        <th class="px-4 py-2.5 text-left">Player</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($goldenBoots as $award)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5">
                            <a href="{{ route('tournaments.show', $award->tournament->year) }}" class="font-medium hover:underline">
                                {{ $award->tournament->year }}
                            </a>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $award->award_name }}</td>
                        <td class="px-4 py-2.5">
                            @if($award->player)
                            <a href="{{ route('players.show', $award->player->player_id) }}" class="hover:underline">
                                {{ $award->player->full_name }}
                                @if($award->player->team) <span class="text-gray-400 text-xs">({{ $award->player->team->flag_emoji }} {{ $award->player->team->team_code }})</span> @endif
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">No data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Biggest wins --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-2">
                <i class="ti ti-sword text-red-500"></i>
                <h2 class="font-semibold text-gray-900">Biggest Wins</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Year</th>
                        <th class="px-4 py-2.5 text-left">Match</th>
                        <th class="px-4 py-2.5 text-center">Score</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($biggestWins as $match)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5">
                            <a href="{{ route('tournaments.show', $match->tournament->year) }}" class="font-medium hover:underline">
                                {{ $match->tournament->year }}
                            </a>
                        </td>
                        <td class="px-4 py-2.5">
                            {{ $match->homeTeam?->flag_emoji }} {{ $match->homeTeam?->team_name }}
                            <span class="text-gray-400">vs</span>
                            {{ $match->awayTeam?->flag_emoji }} {{ $match->awayTeam?->team_name }}
                        </td>
                        <td class="px-4 py-2.5 text-center font-bold text-gray-900">
                            {{ $match->home_score }}–{{ $match->away_score }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
