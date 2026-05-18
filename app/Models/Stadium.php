<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stadium extends Model
{
    protected $primaryKey = 'stadium_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['stadium_id', 'stadium_name', 'city_name', 'country_name', 'capacity'];

    public function matches(): HasMany
    {
        return $this->hasMany(MatchModel::class, 'stadium_id', 'stadium_id');
    }
}
