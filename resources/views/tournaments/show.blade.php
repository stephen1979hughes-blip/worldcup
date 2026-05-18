@extends('layouts.app')

@section('title', $tournament->year . ' FIFA World Cup — ' . $tournament->host_country)
@section('meta_description', $tournament->year . ' FIFA World Cup hosted by ' . $tournament->host_country . '. Groups, scorers, squads and matches.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">
                    <a href="{{ route('tournaments.index') }}" class="hover:underline">Tournaments</a> /
                </p>
                <h1 class="text-3xl font-bold text-gray-900">{{ $tournament->year }} FIFA World Cup</h1>
                <p class="text-gray-500 mt-1">{{ $tournament->host_country }}
                    @if($tournament->start_date) · {{ $tournament->start_date->format('d M') }}–{{ $tournament->end_date?->format('d M Y') }} @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-3 text-sm">
                <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-center">
                    <div class="font-bold text-gray-900">{{ $tournament->num_teams }}</div>
                    <div class="text-gray-500 text-xs">Teams</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-center">
                    <div class="font-bold text-gray-900">{{ $tournament->num_matches }}</div>
                    <div class="text-gray-500 text-xs">Matches</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-center">
                    <div class="font-bold text-gray-900">{{ $tournament->num_goals }}</div>
                    <div class="text-gray-500 text-xs">Goals</div>
                </div>
            </div>
        </div>

        @if($tournament->winner)
        <div class="mt-4 flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-1.5">
                <i class="ti ti-trophy text-yellow-500"></i>
                <span class="font-medium">Winner:</span>
                <a href="{{ route('teams.show', $tournament->winner->team_id) }}" class="hover:underline">
                    {{ $tournament->winner->flag_emoji }} {{ $tournament->winner->team_name }}
                </a>
            </div>
            @if($tournament->runnerUp)
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                <span class="text-gray-500">Runner-up:</span>
                <a href="{{ route('teams.show', $tournament->runnerUp->team_id) }}" class="hover:underline">
                    {{ $tournament->runnerUp->flag_emoji }} {{ $tournament->runnerUp->team_name }}
                </a>
            </div>
            @endif
            @if($tournament->thirdPlace)
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                <span class="text-gray-500">3rd place:</span>
                <a href="{{ route('teams.show', $tournament->thirdPlace->team_id) }}" class="hover:underline">
                    {{ $tournament->thirdPlace->flag_emoji }} {{ $tournament->thirdPlace->team_name }}
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Awards --}}
    @if($tournament->awards->isNotEmpty())
    <div class="flex flex-wrap gap-3 mb-6">
        @foreach($tournament->awards->unique('award_name') as $award)
        @if($award->player)
        <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-sm">
            <span class="text-gray-500">{{ $award->award_name }}:</span>
            <a href="{{ route('players.show', $award->player->player_id) }}" class="font-medium hover:underline ml-1">
                {{ $award->player->full_name }}
            </a>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Tabs --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-0 -mb-px">
            @foreach(['groups' => 'Groups', 'scorers' => 'Top Scorers', 'squads' => 'Squads', 'matches' => 'Matches'] as $tab => $label)
            <button id="btn-{{ $tab }}" onclick="switchTab('{{ $tab }}')"
                    class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors">
                {{ $label }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- Groups panel --}}
    <div id="panel-groups" class="tab-panel">
        @if($groups->isEmpty())
            <p class="text-gray-500 text-sm">No group stage data available.</p>
        @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($groups as $groupName => $groupTeams)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900 text-sm">{{ $groupName }}</h3>
                </div>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100">
                            <th class="px-3 py-1.5 text-left font-medium">Team</th>
                            <th class="px-2 py-1.5 text-center font-medium">P</th>
                            <th class="px-2 py-1.5 text-center font-medium">W</th>
                            <th class="px-2 py-1.5 text-center font-medium">D</th>
                            <th class="px-2 py-1.5 text-center font-medium">L</th>
                            <th class="px-2 py-1.5 text-center font-medium">GD</th>
                            <th class="px-2 py-1.5 text-center font-medium">Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupTeams as $i => $qt)
                        <tr class="border-b border-gray-50 last:border-0 {{ $i < 2 ? 'border-l-2 border-l-green-500' : '' }}">
                            <td class="px-3 py-2">
                                <a href="{{ route('teams.show', $qt->team->team_id) }}" class="flex items-center gap-1.5 hover:underline">
                                    <span>{{ $qt->team->flag_emoji }}</span>
                                    <span class="font-medium text-gray-900">{{ $qt->team->team_name }}</span>
                                </a>
                            </td>
                            <td class="px-2 py-2 text-center text-gray-600">{{ $qt->matches_played }}</td>
                            <td class="px-2 py-2 text-center text-gray-600">{{ $qt->matches_won }}</td>
                            <td class="px-2 py-2 text-center text-gray-600">{{ $qt->matches_drawn }}</td>
                            <td class="px-2 py-2 text-center text-gray-600">{{ $qt->matches_lost }}</td>
                            <td class="px-2 py-2 text-center text-gray-600">{{ $qt->goal_difference >= 0 ? '+' : '' }}{{ $qt->goal_difference }}</td>
                            <td class="px-2 py-2 text-center font-bold text-gray-900">{{ $qt->points }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Scorers panel --}}
    <div id="panel-scorers" class="tab-panel hidden">
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left w-8">#</th>
                        <th class="px-4 py-3 text-left">Player</th>
                        <th class="px-4 py-3 text-left">Team</th>
                        <th class="px-4 py-3 text-center">Goals</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topScorers as $i => $scorer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400 text-sm">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('players.show', $scorer->player_id) }}" class="font-medium text-gray-900 hover:underline">
                                {{ $scorer->player->full_name ?? $scorer->player_id }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($scorer->team)
                            <a href="{{ route('teams.show', $scorer->team->team_id) }}" class="hover:underline">
                                {{ $scorer->team->flag_emoji }} {{ $scorer->team->team_name }}
                            </a>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-lg font-bold text-green-600">{{ $scorer->goals }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No scoring data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Squads panel --}}
    <div id="panel-squads" class="tab-panel hidden">
        <p class="text-sm text-gray-500 mb-4">Select a team to view their squad.</p>
        <div class="flex flex-wrap gap-2">
            @foreach($squadTeams as $team)
            <a href="{{ route('tournaments.squad', [$tournament->year, $team->team_id]) }}"
               class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:border-green-400 hover:shadow-sm transition-all">
                <span>{{ $team->flag_emoji }}</span>
                <span class="font-medium text-gray-700">{{ $team->team_name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Matches panel --}}
    <div id="panel-matches" class="tab-panel hidden">
        @forelse($matches as $stage => $stageMatches)
        <div class="mb-8">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ $stage }}</h3>
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                @foreach($stageMatches as $match)
                <div class="flex items-center px-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50">
                    <div class="flex-1 text-right">
                        <a href="{{ route('teams.show', $match->homeTeam->team_id) }}" class="font-medium text-gray-900 hover:underline">
                            {{ $match->homeTeam->team_name ?? '?' }} {{ $match->homeTeam?->flag_emoji }}
                        </a>
                    </div>
                    <div class="w-28 text-center flex-shrink-0">
                        @if($match->home_score !== null)
                            <a href="{{ route('matches.show', $match->match_id) }}" class="block hover:text-green-600 transition-colors">
                                <span class="text-lg font-bold text-gray-900">{{ $match->home_score }} – {{ $match->away_score }}</span>
                                @if($match->penalties)
                                    <div class="text-xs text-gray-400">(pen {{ $match->home_score_pen }}–{{ $match->away_score_pen }})</div>
                                @endif
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">vs</span>
                        @endif
                        @if($match->match_date)
                            <div class="text-xs text-gray-400">{{ $match->match_date->format('d M') }}</div>
                        @endif
                    </div>
                    <div class="flex-1 text-left">
                        <a href="{{ route('teams.show', $match->awayTeam->team_id) }}" class="font-medium text-gray-900 hover:underline">
                            {{ $match->awayTeam?->flag_emoji }} {{ $match->awayTeam->team_name ?? '?' }}
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-sm">No matches found.</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-green-600', 'text-green-600');
            b.classList.add('border-transparent', 'text-gray-500');
        });
        document.getElementById('panel-' + tabName).classList.remove('hidden');
        const btn = document.getElementById('btn-' + tabName);
        btn.classList.add('border-green-600', 'text-green-600');
        btn.classList.remove('border-transparent', 'text-gray-500');
        history.replaceState(null, '', '#' + tabName);
    }

    const hash = window.location.hash.replace('#', '') || 'groups';
    switchTab(['groups','scorers','squads','matches'].includes(hash) ? hash : 'groups');
</script>
@endpush
@endsection
