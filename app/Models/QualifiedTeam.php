<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualifiedTeam extends Model
{
    protected $fillable = [
        'tournament_id', 'team_id', 'group_name', 'group_stage_result', 'final_position',
        'matches_played', 'matches_won', 'matches_drawn', 'matches_lost',
        'goals_for', 'goals_against', 'goal_difference', 'points',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }
}
