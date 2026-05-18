@extends('layouts.app')

@section('title', $team->team_name . ' — World Cup history')
@section('meta_description', $team->team_name . ' FIFA World Cup history, stats, and all-time top scorers.')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <p class="text-sm text-gray-500 mb-2">
        <a href="{{ route('teams.index') }}" class="hover:underline">Teams</a> /
    </p>

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="text-5xl">{{ $team->flag_emoji }}</div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $team->team_name }}</h1>
            <div class="text-sm text-gray-500">{{ $team->confederation }} · {{ $team->team_code }}</div>
        </div>
    </div>

    {{-- Stats strip --}}
    <div class="grid grid-cols-3 gap-3 mb-8">
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['appearances'] }}</div>
            <div class="text-xs text-gray-500">Appearances</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['titles'] }}</div>
            <div class="text-xs text-gray-500">Titles</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['goals'] }}</div>
            <div class="text-xs text-gray-500">Goals scored</div>
        </div>
    </div>

    {{-- Tournament history --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Tournament History</h2>
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Year</th>
                        <th class="px-4 py-3 text-left">Host</th>
                        <th class="px-4 py-3 text-left">Group</th>
                        <th class="px-4 py-3 text-left">Result</th>
                        <th class="px-4 py-3 text-center">GF</th>
                        <th class="px-4 py-3 text-center">GA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($history as $qt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('tournaments.show', $qt->tournament->year) }}" class="font-medium hover:underline">
                                {{ $qt->tournament->year }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $qt->tournament->host_country }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $qt->group_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($qt->final_position === 'Winner')
                                <span class="inline-flex items-center gap-1 text-yellow-700 font-medium">
                                    🏆 {{ $qt->final_position }}
                                </span>
                            @elseif($qt->final_position)
                                <span class="text-gray-700">{{ $qt->final_position }}</span>
                            @else
                                <span class="text-gray-400">{{ $qt->group_stage_result ?? '—' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $qt->goals_for }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $qt->goals_against }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No history found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- All-time top scorers --}}
    @if($topScorers->isNotEmpty())
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-3">All-time Top Scorers</h2>
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left w-8">#</th>
                        <th class="px-4 py-3 text-left">Player</th>
                        <th class="px-4 py-3 text-center">Goals</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($topScorers as $i => $scorer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('players.show', $scorer->player_id) }}" class="font-medium hover:underline">
                                {{ $scorer->player?->full_name ?? $scorer->player_id }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-green-600">{{ $scorer->goals }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
