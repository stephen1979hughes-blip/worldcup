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

    protected $appends = ['flag_emoji', 'flag_img'];

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

    public function getFlagImgAttribute(): string
    {
        // Map FIFA 3-letter codes to ISO 3166-1 alpha-2 for flagcdn.com
        $iso = [
            'AFG' => 'af', 'ALB' => 'al', 'ALG' => 'dz', 'ANG' => 'ao',
            'ARG' => 'ar', 'AUS' => 'au', 'AUT' => 'at', 'BEL' => 'be',
            'BOL' => 'bo', 'BRA' => 'br', 'BUL' => 'bg', 'CMR' => 'cm',
            'CAN' => 'ca', 'CHI' => 'cl', 'CHN' => 'cn', 'COL' => 'co',
            'CRC' => 'cr', 'CRO' => 'hr', 'CUB' => 'cu', 'CZE' => 'cz',
            'DEN' => 'dk', 'ECU' => 'ec', 'EGY' => 'eg', 'ENG' => 'gb-eng',
            'ESP' => 'es', 'FRA' => 'fr', 'GER' => 'de', 'GHA' => 'gh',
            'GRE' => 'gr', 'HON' => 'hn', 'HRV' => 'hr', 'HUN' => 'hu',
            'IRN' => 'ir', 'IRQ' => 'iq', 'IRL' => 'ie', 'ISL' => 'is',
            'ISR' => 'il', 'ITA' => 'it', 'JAM' => 'jm', 'JPN' => 'jp',
            'KOR' => 'kr', 'KSA' => 'sa', 'KUW' => 'kw', 'MAR' => 'ma',
            'MEX' => 'mx', 'NED' => 'nl', 'NGA' => 'ng', 'NIR' => 'gb-nir',
            'NOR' => 'no', 'NZL' => 'nz', 'PAN' => 'pa', 'PAR' => 'py',
            'PER' => 'pe', 'POL' => 'pl', 'POR' => 'pt', 'QAT' => 'qa',
            'ROM' => 'ro', 'RUS' => 'ru', 'SCO' => 'gb-sct', 'SEN' => 'sn',
            'SLO' => 'si', 'SRB' => 'rs', 'SUI' => 'ch', 'SWE' => 'se',
            'TGA' => 'tg', 'TRI' => 'tt', 'TUN' => 'tn', 'TUR' => 'tr',
            'UAE' => 'ae', 'UKR' => 'ua', 'URU' => 'uy', 'USA' => 'us',
            'WAL' => 'gb-wls', 'YUG' => 'rs', 'ZAI' => 'cd', 'ZAM' => 'zm',
            'SCG' => 'rs', 'TCH' => 'cz', 'URS' => 'ru', 'DDR' => 'de',
            'FRG' => 'de', 'TWN' => 'tw', 'CIV' => 'ci', 'CPV' => 'cv',
            'BIH' => 'ba', 'BGR' => 'bg', 'HRV' => 'hr', 'DZA' => 'dz',
            'CRI' => 'cr',
        ];

        $code = $iso[$this->team_code] ?? null;
        if (! $code) {
            return '<span class="inline-block w-5 h-4 bg-gray-200 rounded-sm align-middle"></span>';
        }

        return '<img src="https://flagcdn.com/20x15/' . $code . '.png" '
             . 'srcset="https://flagcdn.com/40x30/' . $code . '.png 2x" '
             . 'width="20" height="15" alt="' . e($this->team_name) . '" '
             . 'class="inline-block rounded-sm align-middle">';
    }
}
