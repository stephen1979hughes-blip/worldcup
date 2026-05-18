<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerAppointment extends Model
{
    protected $fillable = ['tournament_id', 'team_id', 'manager_id'];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'tournament_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'manager_id', 'manager_id');
    }
}
