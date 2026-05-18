<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    protected $primaryKey = 'tournament_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tournament_id', 'gender', 'year', 'host_country', 'host_continent',
        'winner_team_id', 'runner_up_team_id', 'third_place_team_id', 'fourth_place_team_id',
        'start_date', 'end_date', 'num_teams', 'num_matches', 'num_goals', 'format',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeForGender(\Illuminate\Database\Eloquent\Builder $query, string $gender): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('gender', $gender);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_team_id', 'team_id');
    }

    public function runnerUp(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'runner_up_team_id', 'team_id');
    }

    public function thirdPlace(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'third_place_team_id', 'team_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchModel::class, 'tournament_id', 'tournament_id');
    }

    public function qualifiedTeams(): HasMany
    {
        return $this->hasMany(QualifiedTeam::class, 'tournament_id', 'tournament_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class, 'tournament_id', 'tournament_id');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class, 'tournament_id', 'tournament_id');
    }

    public function squads(): HasMany
    {
        return $this->hasMany(Squad::class, 'tournament_id', 'tournament_id');
    }

    public function topScorers(int $limit = 10): Collection
    {
        return Goal::where('tournament_id', $this->tournament_id)
            ->where('own_goal', false)
            ->selectRaw('player_id, team_id, count(*) as goals')
            ->groupBy('player_id', 'team_id')
            ->orderByDesc('goals')
            ->limit($limit)
            ->with(['player', 'team'])
            ->get();
    }
}
