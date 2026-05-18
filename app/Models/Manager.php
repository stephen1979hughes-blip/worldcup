<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manager extends Model
{
    protected $primaryKey = 'manager_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['manager_id', 'given_name', 'family_name', 'team_id', 'home_country'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(ManagerAppointment::class, 'manager_id', 'manager_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->given_name
            ? trim($this->given_name . ' ' . $this->family_name)
            : $this->family_name;
    }
}
