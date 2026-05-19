@extends('layouts.app')

@section('title', ($match->homeTeam->team_name ?? '?') . ' vs ' . ($match->awayTeam->team_name ?? '?') . ' — ' . $match->tournament->year . ' World Cup')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <p class="text-sm text-gray-500 mb-4">
        <a href="{{ route('tournaments.show', $match->tournament->year) }}" class="hover:underline">{{ $match->tournament->year }} World Cup</a>
        / <a href="{{ route('tournaments.show', $match->tournament->year) }}#matches" class="hover:underline">Matches</a>
        /
    </p>

    {{-- Score card --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-6">
        {{-- Stage + date --}}
        <div class="text-center text-xs text-gray-400 uppercase tracking-wider mb-4">
            {{ $match->stage_name }}
            @if($match->group_name) · {{ $match->group_name }} @endif
            @if($match->match_date) · {{ $match->match_date->format('d F Y') }} @endif
            @if($match->stadium) · {{ $match->stadium->stadium_name }}, {{ $match->stadium->city_name }} @endif
        </div>

        {{-- Teams + score --}}
        <div class="flex items-center justify-between gap-4">
            {{-- Home team --}}
            <div class="flex-1 text-right">
                <a href="{{ route('teams.show', $match->homeTeam->team_id) }}" class="hover:underline">
                    <div class="text-4xl mb-1">{!! $match->homeTeam->flag_img !!}</div>
                    <div class="text-lg font-bold text-gray-900">{{ $match->homeTeam->team_name }}</div>
                </a>
            </div>

            {{-- Score --}}
            <div class="text-center flex-shrink-0 px-4">
                @if($match->home_score !== null)
                    <div class="text-5xl font-black text-gray-900 tabular-nums">
                        {{ $match->home_score }}<span class="text-gray-300 mx-1">–</span>{{ $match->away_score }}
                    </div>
                    @if($match->home_score_et !== null && !$match->penalties)
                        <div class="text-xs text-gray-400 mt-1">After extra time</div>
                    @endif
                    @if($match->penalties)
                        <div class="text-xs text-gray-500 mt-1 font-medium">
                            Pen: {{ $match->home_score_pen }}–{{ $match->away_score_pen }}
                        </div>
                    @endif
                @else
                    <div class="text-2xl text-gray-300">vs</div>
                @endif
            </div>

            {{-- Away team --}}
            <div class="flex-1 text-left">
                <a href="{{ route('teams.show', $match->awayTeam->team_id) }}" class="hover:underline">
                    <div class="text-4xl mb-1">{!! $match->awayTeam->flag_img !!}</div>
                    <div class="text-lg font-bold text-gray-900">{{ $match->awayTeam->team_name }}</div>
                </a>
            </div>
        </div>

        {{-- Goal scorers summary below score --}}
        @if($goals->isNotEmpty())
        <div class="mt-5 pt-4 border-t border-gray-100 flex justify-between text-sm text-gray-600">
            <div class="flex-1 text-right pr-8 space-y-0.5">
                @foreach($homeGoals->merge($homeOwnGoals) as $g)
                <div>
                    {{ $g->player?->full_name ?? '?' }}
                    @if($g->own_goal) <span class="text-red-400 text-xs">(og)</span> @endif
                    @if($g->penalty) <span class="text-blue-400 text-xs">(pen)</span> @endif
                    <span class="text-gray-400 text-xs">{{ $g->minute }}'</span>
                </div>
                @endforeach
            </div>
            <div class="flex-1 text-left pl-8 space-y-0.5">
                @foreach($awayGoals->merge($awayOwnGoals) as $g)
                <div>
                    <span class="text-gray-400 text-xs">{{ $g->minute }}'</span>
                    @if($g->penalty) <span class="text-blue-400 text-xs">(pen)</span> @endif
                    @if($g->own_goal) <span class="text-red-400 text-xs">(og)</span> @endif
                    {{ $g->player?->full_name ?? '?' }}
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Goal timeline --}}
    @if($goals->isNotEmpty())
    <div class="bg-white border border-gray-200 rounded-xl mb-6 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <span class="text-base">⚽</span>
            <h2 class="font-semibold text-gray-900">Goals</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($goals as $goal)
            <div class="flex items-center px-5 py-3 gap-4">
                <div class="w-10 text-center flex-shrink-0">
                    <span class="text-sm font-mono font-medium text-gray-500">{{ $goal->minute ?? '?' }}'</span>
                    @if($goal->minute_stoppage)
                        <span class="text-xs text-gray-400">+{{ $goal->minute_stoppage }}</span>
                    @endif
                </div>
                <div class="flex-1 flex items-center gap-2 {{ $goal->team_id === $match->home_team_id ? '' : 'flex-row-reverse text-right' }}">
                    <span class="text-lg">{{ $goal->own_goal ? '🔴' : ($goal->penalty ? '🎯' : '⚽') }}</span>
                    <div>
                        @if($goal->player)
                        <a href="{{ route('players.show', $goal->player->player_id) }}" class="font-medium text-gray-900 hover:underline">
                            {{ $goal->player->full_name }}
                        </a>
                        @else
                        <span class="font-medium text-gray-900">Unknown</span>
                        @endif
                        <span class="text-xs text-gray-400 ml-1">
                            @if($goal->own_goal)(own goal)@endif
                            @if($goal->penalty)(penalty)@endif
                        </span>
                    </div>
                </div>
                <div class="w-20 text-center flex-shrink-0">
                    <span class="text-xs text-gray-400">{{ $goal->team->flag_emoji ?? '' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Bookings --}}
    @if($bookings->isNotEmpty())
    <div class="bg-white border border-gray-200 rounded-xl mb-6 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <span class="text-base">🟨</span>
            <h2 class="font-semibold text-gray-900">Bookings</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($bookings as $booking)
            <div class="flex items-center px-5 py-3 gap-4">
                <div class="w-10 text-center flex-shrink-0">
                    <span class="text-sm font-mono font-medium text-gray-500">{{ $booking->minute ?? '?' }}'</span>
                </div>
                <div class="flex-1 flex items-center gap-2">
                    <span>
                        @if($booking->booking_type === 'red_card') 🟥
                        @elseif($booking->booking_type === 'second_yellow') 🟨🟥
                        @else 🟨
                        @endif
                    </span>
                    @if($booking->player)
                    <a href="{{ route('players.show', $booking->player->player_id) }}" class="font-medium text-gray-900 hover:underline">
                        {{ $booking->player->full_name }}
                    </a>
                    @endif
                    <span class="text-xs text-gray-400">{{ $booking->team->flag_emoji ?? '' }} {{ $booking->team->team_name ?? '' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Lineups --}}
    @if($homeAppearances->isNotEmpty() || $awayAppearances->isNotEmpty())
    <div class="mb-2 flex items-center gap-2">
        <h2 class="font-semibold text-gray-900">
            {{ $lineupSource === 'squad' ? 'Tournament Squads' : 'Lineups' }}
        </h2>
        @if($lineupSource === 'squad')
        <span class="text-xs bg-amber-100 text-amber-700 rounded-full px-2 py-0.5">
            Match lineup data not available — showing full tournament squad
        </span>
        @endif
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        @foreach([['team' => $match->homeTeam, 'appearances' => $homeAppearances], ['team' => $match->awayTeam, 'appearances' => $awayAppearances]] as $side)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <span>{!! $side['team']->flag_img !!}</span>
                <h2 class="font-semibold text-gray-900 text-sm">{{ $side['team']->team_name }}</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($side['appearances'] as $app)
                <div class="flex items-center px-4 py-2 gap-3">
                    @if($lineupSource === 'appearances')
                        <span class="text-xs w-12 text-gray-400 flex-shrink-0">{{ $app->starter ? 'Start' : 'Sub' }}</span>
                    @else
                        <span class="text-xs w-8 text-gray-400 flex-shrink-0 text-center">{{ $app->shirt_number }}</span>
                        @if($app->position_code)
                            <x-position-badge :position="$app->position_code" />
                        @endif
                    @endif
                    @if($app->player)
                    <a href="{{ route('players.show', $app->player->player_id) }}" class="text-sm font-medium text-gray-900 hover:underline">
                        {{ $app->player->full_name }}
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
