@extends('layouts.app')

@section('title', $player->full_name . ' — World Cup career')
@section('meta_description', $player->full_name . ' World Cup career stats, goals, and tournament history.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <p class="text-sm text-gray-500 mb-2">
        <a href="{{ route('players.index') }}" class="hover:underline">Players</a> /
    </p>

    {{-- Player header --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="w-16 h-16 rounded-full bg-green-100 text-green-800 flex items-center justify-center text-xl font-bold flex-shrink-0">
            {{ $player->initials }}
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $player->full_name }}</h1>
            @if($player->team)
            <a href="{{ route('teams.show', $player->team->team_id) }}" class="text-gray-500 hover:underline">
                {{ $player->team->flag_emoji }} {{ $player->team->team_name }}
            </a>
            @endif
            @if($player->birth_year)
            <span class="text-gray-400 text-sm ml-3">b. {{ $player->birth_year }}</span>
            @endif
        </div>
    </div>

    {{-- Career stats strip --}}
    <div class="grid grid-cols-3 gap-3 mb-8">
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $appearances->count() }}</div>
            <div class="text-xs text-gray-500">Tournaments</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $goals->count() }}</div>
            <div class="text-xs text-gray-500">Goals</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $bookings->where('booking_type', 'yellow_card')->count() }}</div>
            <div class="text-xs text-gray-500">Yellow cards</div>
        </div>
    </div>

    {{-- Tournament history --}}
    @if($appearances->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Tournament History</h2>
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Year</th>
                        <th class="px-4 py-3 text-left">Host</th>
                        <th class="px-4 py-3 text-left">Position</th>
                        <th class="px-4 py-3 text-center">Goals</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($appearances as $squad)
                    @php $tGoals = $goals->where('tournament_id', $squad->tournament_id)->count(); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('tournaments.show', $squad->tournament->year) }}" class="font-medium hover:underline">
                                {{ $squad->tournament->year }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $squad->tournament->host_country }}</td>
                        <td class="px-4 py-3">
                            @if($squad->position_code)
                                <x-position-badge :position="$squad->position_code" />
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-bold {{ $tGoals > 0 ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $tGoals ?: '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Goals list --}}
    @if($goals->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Goals ({{ $goals->count() }})</h2>
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Tournament</th>
                        <th class="px-4 py-3 text-left">Match</th>
                        <th class="px-4 py-3 text-center">Min</th>
                        <th class="px-4 py-3 text-left">Type</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($goals as $goal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5">
                            <a href="{{ route('tournaments.show', $goal->tournament->year) }}" class="hover:underline">
                                {{ $goal->tournament->year }}
                            </a>
                        </td>
                        <td class="px-4 py-2.5">
                            @if($goal->match)
                                <a href="{{ route('matches.show', $goal->match->match_id) }}"
                                   class="text-gray-700 hover:underline hover:text-gray-900 font-medium">
                                    {{ $goal->match->homeTeam?->flag_emoji }}
                                    <span class="font-bold">{{ $goal->match->home_score }}–{{ $goal->match->away_score }}</span>
                                    {{ $goal->match->awayTeam?->flag_emoji }}
                                </a>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-center text-gray-500">{{ $goal->minute ?? '?' }}'</td>
                        <td class="px-4 py-2.5">
                            @if($goal->penalty)
                                <span class="text-xs bg-blue-100 text-blue-700 rounded px-1.5 py-0.5">Penalty</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
