<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    protected $primaryKey = 'player_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['player_id', 'given_name', 'family_name', 'team_id', 'birth_year', 'goal_keeper'];

    protected $appends = ['full_name', 'initials'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    public function squads(): HasMany
    {
        return $this->hasMany(Squad::class, 'player_id', 'player_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class, 'player_id', 'player_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'player_id', 'player_id');
    }

    public function appearances(): HasMany
    {
        return $this->hasMany(PlayerAppearance::class, 'player_id', 'player_id');
    }

    public function getFullNameAttribute(): string
    {
        $given = ($this->given_name && $this->given_name !== 'not applicable')
            ? $this->given_name
            : null;

        return $given ? trim($given . ' ' . $this->family_name) : $this->family_name;
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->full_name);
        $first = strtoupper(substr($parts[0], 0, 1));
        $last = strtoupper(substr(end($parts), 0, 1));
        return $first !== $last ? $first . $last : $first;
    }

    public function totalWcGoals(): int
    {
        return $this->goals()->where('own_goal', false)->count();
    }

    public function tournamentCount(): int
    {
        return $this->squads()->distinct('tournament_id')->count('tournament_id');
    }
}
