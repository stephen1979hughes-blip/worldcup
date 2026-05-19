@extends('layouts.app')

@section('title', 'WorldCupDB')
@section('meta_description', 'Browse squads, groups, scorers and records from all 22 FIFA World Cup tournaments, 1930–2022.')

@section('content')
{{-- Hero --}}
<div class="bg-gradient-to-br from-green-700 to-green-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h1 class="text-4xl font-bold mb-3">Every World Cup. Every Player.</h1>
        <p class="text-green-200 text-lg mb-8">Complete data from all 22 FIFA World Cup tournaments, 1930–2022.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <div class="bg-white/10 rounded-xl px-6 py-3 text-center">
                <div class="text-2xl font-bold">{{ number_format($stats['tournaments']) }}</div>
                <div class="text-green-200 text-sm">Tournaments</div>
            </div>
            <div class="bg-white/10 rounded-xl px-6 py-3 text-center">
                <div class="text-2xl font-bold">{{ number_format($stats['goals']) }}</div>
                <div class="text-green-200 text-sm">Goals</div>
            </div>
            <div class="bg-white/10 rounded-xl px-6 py-3 text-center">
                <div class="text-2xl font-bold">{{ number_format($stats['players']) }}</div>
                <div class="text-green-200 text-sm">Players</div>
            </div>
            <div class="bg-white/10 rounded-xl px-6 py-3 text-center">
                <div class="text-2xl font-bold">{{ number_format($stats['teams']) }}</div>
                <div class="text-green-200 text-sm">Teams</div>
            </div>
        </div>
    </div>
</div>

{{-- Tournament grid --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Tournaments</h2>
        <a href="{{ route('tournaments.index') }}" class="text-sm text-green-600 hover:underline">View all</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($tournaments as $t)
        <a href="{{ route('tournaments.show', $t->year) }}"
           class="bg-white border border-gray-200 rounded-xl p-4 hover:border-green-400 hover:shadow-sm transition-all text-center group {{ $t->year === 2022 ? 'border-green-400 ring-1 ring-green-400' : '' }}">
            @if($t->year === 2022)
                <span class="inline-block text-xs bg-green-100 text-green-700 rounded-full px-2 py-0.5 mb-1 font-medium">Latest</span>
            @endif
            <div class="text-2xl font-bold text-gray-900 group-hover:text-green-600">{{ $t->year }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $t->host_country }}</div>
            @if($t->winner)
            <div class="mt-2 text-xs text-gray-700 flex items-center justify-center gap-1">
                <i class="ti ti-trophy text-yellow-500 text-xs"></i>
                <span>{!! $t->winner->flag_img !!} {{ $t->winner->team_name }}</span>
            </div>
            @endif
            <div class="mt-1.5 text-xs text-gray-400">{{ $t->num_teams }} teams · {{ $t->num_matches }} matches</div>
        </a>
        @endforeach
    </div>
</div>

{{-- Quick links --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Explore</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('players.index') }}" class="bg-white border border-gray-200 rounded-xl p-6 hover:border-green-400 hover:shadow-sm transition-all group">
            <i class="ti ti-user text-2xl text-green-600 mb-3 block"></i>
            <div class="font-semibold text-gray-900">Players</div>
            <div class="text-sm text-gray-500 mt-1">Search all {{ number_format($stats['players']) }} players</div>
        </a>
        <a href="{{ route('teams.index') }}" class="bg-white border border-gray-200 rounded-xl p-6 hover:border-green-400 hover:shadow-sm transition-all group">
            <i class="ti ti-shield text-2xl text-green-600 mb-3 block"></i>
            <div class="font-semibold text-gray-900">Teams</div>
            <div class="text-sm text-gray-500 mt-1">{{ number_format($stats['teams']) }} national teams</div>
        </a>
        <a href="{{ route('records') }}" class="bg-white border border-gray-200 rounded-xl p-6 hover:border-green-400 hover:shadow-sm transition-all group">
            <i class="ti ti-award text-2xl text-green-600 mb-3 block"></i>
            <div class="font-semibold text-gray-900">Records</div>
            <div class="text-sm text-gray-500 mt-1">All-time top scorers & awards</div>
        </a>
        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-6 text-center">
            <i class="ti ti-device-gamepad-2 text-2xl text-gray-400 mb-3 block"></i>
            <div class="font-semibold text-gray-400">Game</div>
            <div class="text-sm text-gray-400 mt-1">Coming soon</div>
        </div>
    </div>
</div>
@endsection
