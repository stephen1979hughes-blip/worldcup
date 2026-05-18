<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchModel extends Model
{
    protected $table = 'matches';

    protected $primaryKey = 'match_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'match_id', 'tournament_id', 'stage_name', 'group_name', 'match_number',
        'match_date', 'stadium_id', 'home_team_id', 'away_team_id',
        'home_score', 'away_score', 'home_score_et', 'away_score_et',
        'penalties', 'home_score_pen', 'away_score_pen', 'result', 'attendance',
    ];

    protected $casts = [
        'match_date' => 'date',
        'penalties' => 'boolean',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id', 'team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id', 'team_id');
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class, 'stadium_id', 'stadium_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class, 'match_id', 'match_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'match_id', 'match_id');
    }

    public function getScoreDisplayAttribute(): string
    {
        if ($this->home_score === null) {
            return 'vs';
        }

        $score = $this->home_score . ' – ' . $this->away_score;

        if ($this->penalties) {
            $score .= ' (pen: ' . $this->home_score_pen . '–' . $this->away_score_pen . ')';
        } elseif ($this->home_score_et !== null) {
            $score .= ' (a.e.t.)';
        }

        return $score;
    }
}
