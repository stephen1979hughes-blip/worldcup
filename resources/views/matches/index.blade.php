@extends('layouts.app')

@section('title', 'Matches — WorldCupDB')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">Matches</h1>

    {{-- Filters --}}
    <form method="GET" action="{{ route('matches.index') }}" class="bg-white border border-gray-200 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
                <select name="year" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All years</option>
                    @foreach($tournaments as $t)
                        <option value="{{ $t->year }}" {{ request('year') == $t->year ? 'selected' : '' }}>
                            {{ $t->year }} — {{ $t->host_country }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Team</label>
                <select name="team" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All teams</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->team_id }}" {{ request('team') == $team->team_id ? 'selected' : '' }}>
                            {{ $team->team_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Stage</label>
                <select name="stage" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All stages</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>
                            {{ $stage }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex gap-2 mt-3">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                Search
            </button>
            @if(request()->hasAny(['year', 'team', 'stage']))
            <a href="{{ route('matches.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Clear
            </a>
            @endif
        </div>
    </form>

    {{-- Results --}}
    <div class="text-xs text-gray-400 mb-3">{{ $matches->total() }} matches</div>

    @if($matches->isEmpty())
        <div class="text-center py-16 text-gray-400">No matches found.</div>
    @else
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @foreach($matches as $match)
        <div class="flex items-center px-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50">
            {{-- Date + tournament --}}
            <div class="w-24 flex-shrink-0 text-xs text-gray-400">
                @if($match->match_date)
                    <div>{{ $match->match_date->format('d M Y') }}</div>
                @endif
                <div class="text-gray-300">{{ $match->tournament->year }}</div>
            </div>

            {{-- Stage --}}
            <div class="w-28 flex-shrink-0 hidden sm:block">
                <span class="text-xs text-gray-400">{{ $match->stage_name }}</span>
            </div>

            {{-- Home team --}}
            <div class="flex-1 text-right flex items-center justify-end gap-2">
                <span class="text-sm font-medium text-gray-900">{{ $match->homeTeam?->team_name ?? '?' }}</span>
                {!! $match->homeTeam?->flag_img !!}
            </div>

            {{-- Score --}}
            <div class="w-24 text-center flex-shrink-0 px-2">
                @if($match->home_score !== null)
                    <a href="{{ route('matches.show', $match->match_id) }}"
                       class="text-base font-bold text-gray-900 hover:text-green-600 transition-colors tabular-nums">
                        {{ $match->home_score }} – {{ $match->away_score }}
                    </a>
                    @if($match->penalties)
                        <div class="text-xs text-gray-400">(pen {{ $match->home_score_pen }}–{{ $match->away_score_pen }})</div>
                    @endif
                @else
                    <span class="text-gray-300 text-sm">vs</span>
                @endif
            </div>

            {{-- Away team --}}
            <div class="flex-1 text-left flex items-center gap-2">
                {!! $match->awayTeam?->flag_img !!}
                <span class="text-sm font-medium text-gray-900">{{ $match->awayTeam?->team_name ?? '?' }}</span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($matches->hasPages())
    <div class="mt-4">
        {{ $matches->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
