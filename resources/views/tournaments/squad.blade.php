@extends('layouts.app')

@section('title', $team->team_name . ' — ' . $tournament->year . ' World Cup Squad')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <p class="text-sm text-gray-500 mb-2">
        <a href="{{ route('tournaments.show', $tournament->year) }}" class="hover:underline">{{ $tournament->year }} World Cup</a>
        / Squads /
    </p>
    <div class="flex items-center gap-3 mb-6">
        <span class="text-4xl">{{ $team->flag_emoji }}</span>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $team->team_name }}</h1>
            @if($manager?->manager)
            <p class="text-sm text-gray-500">Manager: <span class="font-medium text-gray-700">{{ $manager->manager->full_name }}</span></p>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
                <tr>
                    <th class="px-4 py-3 text-center w-10">#</th>
                    <th class="px-4 py-3 text-left w-16">Pos</th>
                    <th class="px-4 py-3 text-left">Player</th>
                    <th class="px-4 py-3 text-center">Born</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($squad as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-center text-sm text-gray-400">{{ $entry->shirt_number }}</td>
                    <td class="px-4 py-3">
                        @if($entry->position_code)
                            <x-position-badge :position="$entry->position_code" />
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($entry->player)
                        <a href="{{ route('players.show', $entry->player->player_id) }}" class="font-medium text-gray-900 hover:underline">
                            {{ $entry->player->full_name }}
                        </a>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $entry->player?->birth_year ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No squad data available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('tournaments.show', $tournament->year) }}#squads" class="text-sm text-green-600 hover:underline">
            ← Back to all squads
        </a>
    </div>
</div>
@endsection
