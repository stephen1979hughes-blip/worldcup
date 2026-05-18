@extends('layouts.app')

@section('title', 'Tournaments')
@section('meta_description', 'All 22 FIFA World Cup tournaments from 1930 to 2022.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">All Tournaments</h1>

    <div class="grid gap-3">
        @foreach($tournaments as $t)
        <a href="{{ route('tournaments.show', $t->year) }}"
           class="bg-white border border-gray-200 rounded-xl px-6 py-4 flex items-center gap-6 hover:border-green-400 hover:shadow-sm transition-all">
            <div class="text-3xl font-bold text-gray-300 w-20 flex-shrink-0">{{ $t->year }}</div>
            <div class="flex-1">
                <div class="font-semibold text-gray-900">{{ $t->host_country }}</div>
                <div class="text-sm text-gray-500">{{ $t->num_teams }} teams · {{ $t->num_matches }} matches · {{ $t->num_goals }} goals</div>
            </div>
            @if($t->winner)
            <div class="text-right flex-shrink-0">
                <div class="flex items-center gap-1.5 text-sm font-medium text-gray-700">
                    <i class="ti ti-trophy text-yellow-500"></i>
                    {{ $t->winner->flag_emoji }} {{ $t->winner->team_name }}
                </div>
            </div>
            @endif
            <i class="ti ti-chevron-right text-gray-400 flex-shrink-0"></i>
        </a>
        @endforeach
    </div>
</div>
@endsection
