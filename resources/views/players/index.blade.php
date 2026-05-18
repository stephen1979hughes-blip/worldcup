@extends('layouts.app')

@section('title', 'Players')
@section('meta_description', 'Search all FIFA World Cup players. Filter by tournament, position, and team.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Players</h1>

    {{-- Filters --}}
    <form method="GET" action="{{ route('players.index') }}" class="bg-white border border-gray-200 rounded-xl p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tournament</label>
            <select name="year" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-500" onchange="this.form.submit()">
                <option value="">All years</option>
                @foreach($tournaments as $t)
                <option value="{{ $t->year }}" {{ request('year') == $t->year ? 'selected' : '' }}>
                    {{ $t->year }} {{ $t->host_country }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Position</label>
            <select name="position" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-500" onchange="this.form.submit()">
                <option value="">All positions</option>
                @foreach(['GK' => 'Goalkeeper', 'DF' => 'Defender', 'MF' => 'Midfielder', 'FW' => 'Forward'] as $code => $label)
                <option value="{{ $code }}" {{ request('position') === $code ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Team</label>
            <select name="team" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-500" onchange="this.form.submit()">
                <option value="">All teams</option>
                @foreach($teams as $team)
                <option value="{{ $team->team_id }}" {{ request('team') === $team->team_id ? 'selected' : '' }}>
                    {{ $team->team_name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-48">
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Player name..."
                   class="w-full text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <button type="submit" class="px-4 py-1.5 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">Search</button>
        @if(request()->anyFilled(['year','position','team','search']))
        <a href="{{ route('players.index') }}" class="px-4 py-1.5 text-sm border border-gray-200 rounded-lg hover:bg-gray-50">Clear</a>
        @endif
    </form>

    <p class="text-sm text-gray-500 mb-4">{{ number_format($players->total()) }} players found</p>

    {{-- Player grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-8">
        @forelse($players as $player)
        @php
            $posCode = null; // position not directly on player; would need a join — skip colour for now
            $avatarBg = 'bg-green-100 text-green-800';
        @endphp
        <a href="{{ route('players.show', $player->player_id) }}"
           class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:border-green-400 hover:shadow-sm transition-all">
            <div class="w-10 h-10 rounded-full {{ $avatarBg }} flex items-center justify-center text-sm font-bold mx-auto mb-2">
                {{ $player->initials }}
            </div>
            <div class="text-sm font-semibold text-gray-900 truncate">{{ $player->family_name }}</div>
            @if($player->given_name)
            <div class="text-xs text-gray-400 truncate">{{ $player->given_name }}</div>
            @endif
            <div class="text-xs text-gray-500 mt-1">{{ $player->team?->flag_emoji }} {{ $player->team?->team_code }}</div>
            <div class="mt-2 flex justify-center gap-2 text-xs text-gray-500">
                <span title="Tournaments">🏆 {{ $player->tournament_count }}</span>
                <span title="Goals">⚽ {{ $player->wc_goals }}</span>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12 text-gray-400">No players found.</div>
        @endforelse
    </div>

    {{ $players->links() }}
</div>
@endsection
