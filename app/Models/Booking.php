<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'booking_id', 'tournament_id', 'match_id', 'team_id', 'player_id',
        'booking_type', 'minute',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id', 'match_id');
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
