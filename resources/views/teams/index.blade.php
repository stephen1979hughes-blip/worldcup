@extends('layouts.app')

@section('title', 'Teams')
@section('meta_description', 'All national teams that have appeared at the FIFA World Cup.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Teams</h1>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        @foreach($teams as $team)
        <a href="{{ route('teams.show', $team->team_id) }}"
           class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:border-green-400 hover:shadow-sm transition-all">
            <div class="text-3xl mb-2">{{ $team->flag_emoji }}</div>
            <div class="text-sm font-semibold text-gray-900">{{ $team->team_name }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $team->confederation }}</div>
            <div class="mt-2 flex justify-center gap-3 text-xs text-gray-500">
                <span title="World Cup appearances">{{ $team->appearances }}×</span>
                @if($team->titles_count > 0)
                <span title="World Cup titles" class="text-yellow-600">
                    {{ str_repeat('🏆', $team->titles_count) }}
                </span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
