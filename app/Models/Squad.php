<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Squad extends Model
{
    protected $fillable = [
        'tournament_id', 'team_id', 'player_id',
        'position_name', 'position_code', 'shirt_number',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id', 'player_id');
    }
}
