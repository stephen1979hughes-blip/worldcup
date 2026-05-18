<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $primaryKey = 'team_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['team_id', 'team_name', 'team_code', 'confederation', 'region'];

    protected $appends = ['flag_emoji'];

    public function qualifiedTournaments(): HasMany
    {
        return $this->hasMany(QualifiedTeam::class, 'team_id', 'team_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class, 'team_id', 'team_id')
            ->where('own_goal', false);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class, 'team_id', 'team_id');
    }

    public function titles(): int
    {
        return Tournament::where('winner_team_id', $this->team_id)->count();
    }

    public function getFlagEmojiAttribute(): string
    {
        $flags = [
            'AFG' => '🇦🇫', 'ALB' => '🇦🇱', 'ALG' => '🇩🇿', 'ANG' => '🇦🇴',
            'ARG' => '🇦🇷', 'AUS' => '🇦🇺', 'AUT' => '🇦🇹', 'BEL' => '🇧🇪',
            'BOL' => '🇧🇴', 'BRA' => '🇧🇷', 'BUL' => '🇧🇬', 'CMR' => '🇨🇲',
            'CAN' => '🇨🇦', 'CHI' => '🇨🇱', 'CHN' => '🇨🇳', 'COL' => '🇨🇴',
            'CRC' => '🇨🇷', 'CRO' => '🇭🇷', 'CUB' => '🇨🇺', 'CZE' => '🇨🇿',
            'DEN' => '🇩🇰', 'ECU' => '🇪🇨', 'EGY' => '🇪🇬', 'ENG' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
            'ESP' => '🇪🇸', 'FRA' => '🇫🇷', 'GER' => '🇩🇪', 'GHA' => '🇬🇭',
            'GRE' => '🇬🇷', 'HON' => '🇭🇳', 'HRV' => '🇭🇷', 'HUN' => '🇭🇺',
            'IRN' => '🇮🇷', 'IRQ' => '🇮🇶', 'IRL' => '🇮🇪', 'ISL' => '🇮🇸',
            'ISR' => '🇮🇱', 'ITA' => '🇮🇹', 'JAM' => '🇯🇲', 'JPN' => '🇯🇵',
            'KOR' => '🇰🇷', 'KSA' => '🇸🇦', 'KUW' => '🇰🇼', 'MAR' => '🇲🇦',
            'MEX' => '🇲🇽', 'NED' => '🇳🇱', 'NGA' => '🇳🇬', 'NIR' => '🇬🇧',
            'NOR' => '🇳🇴', 'NZL' => '🇳🇿', 'PAN' => '🇵🇦', 'PAR' => '🇵🇾',
            'PER' => '🇵🇪', 'POL' => '🇵🇱', 'POR' => '🇵🇹', 'QAT' => '🇶🇦',
            'ROM' => '🇷🇴', 'RUS' => '🇷🇺', 'SCO' => '🏴󠁧󠁢󠁳󠁣󠁴󠁿', 'SEN' => '🇸🇳',
            'SLO' => '🇸🇮', 'SRB' => '🇷🇸', 'SUI' => '🇨🇭', 'SWE' => '🇸🇪',
            'TGA' => '🇹🇬', 'TRI' => '🇹🇹', 'TUN' => '🇹🇳', 'TUR' => '🇹🇷',
            'UAE' => '🇦🇪', 'UKR' => '🇺🇦', 'URU' => '🇺🇾', 'USA' => '🇺🇸',
            'WAL' => '🏴󠁧󠁢󠁷󠁬󠁳󠁿', 'YUG' => '🇷🇸', 'ZAI' => '🇨🇩', 'ZAM' => '🇿🇲',
        ];

        return $flags[$this->team_code] ?? '🏳';
    }
}
