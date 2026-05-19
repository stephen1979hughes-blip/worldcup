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
            'DZA' => '🇩🇿', 'AGO' => '🇦🇴', 'ARG' => '🇦🇷', 'AUS' => '🇦🇺',
            'AUT' => '🇦🇹', 'BEL' => '🇧🇪', 'BOL' => '🇧🇴', 'BIH' => '🇧🇦',
            'BRA' => '🇧🇷', 'BGR' => '🇧🇬', 'CMR' => '🇨🇲', 'CAN' => '🇨🇦',
            'CHL' => '🇨🇱', 'CHN' => '🇨🇳', 'TWN' => '🇹🇼', 'COL' => '🇨🇴',
            'CRI' => '🇨🇷', 'HRV' => '🇭🇷', 'CUB' => '🇨🇺', 'CZE' => '🇨🇿',
            'CSK' => '🇨🇿', 'DNK' => '🇩🇰', 'IDN' => '🇮🇩', 'DDR' => '🇩🇪',
            'ECU' => '🇪🇨', 'EGY' => '🇪🇬', 'SLV' => '🇸🇻', 'ENG' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
            'FRA' => '🇫🇷', 'DEU' => '🇩🇪', 'GHA' => '🇬🇭', 'GRC' => '🇬🇷',
            'GNQ' => '🇬🇶', 'HTI' => '🇭🇹', 'HND' => '🇭🇳', 'HUN' => '🇭🇺',
            'ISL' => '🇮🇸', 'IRN' => '🇮🇷', 'IRQ' => '🇮🇶', 'IRL' => '🇮🇪',
            'ISR' => '🇮🇱', 'ITA' => '🇮🇹', 'CIV' => '🇨🇮', 'JAM' => '🇯🇲',
            'JPN' => '🇯🇵', 'KWT' => '🇰🇼', 'MEX' => '🇲🇽', 'MAR' => '🇲🇦',
            'NLD' => '🇳🇱', 'NZL' => '🇳🇿', 'NGA' => '🇳🇬', 'PRK' => '🇰🇵',
            'NIR' => '🇬🇧', 'NOR' => '🇳🇴', 'PAN' => '🇵🇦', 'PRY' => '🇵🇾',
            'PER' => '🇵🇪', 'POL' => '🇵🇱', 'PRT' => '🇵🇹', 'QAT' => '🇶🇦',
            'ROU' => '🇷🇴', 'RUS' => '🇷🇺', 'SAU' => '🇸🇦', 'SCO' => '🏴󠁧󠁢󠁳󠁣󠁴󠁿',
            'SEN' => '🇸🇳', 'SRB' => '🇷🇸', 'SCG' => '🇷🇸', 'SVK' => '🇸🇰',
            'SVN' => '🇸🇮', 'ZAF' => '🇿🇦', 'KOR' => '🇰🇷', 'SUN' => '🇷🇺',
            'ESP' => '🇪🇸', 'SWE' => '🇸🇪', 'CHE' => '🇨🇭', 'THA' => '🇹🇭',
            'TGO' => '🇹🇬', 'TTO' => '🇹🇹', 'TUN' => '🇹🇳', 'TUR' => '🇹🇷',
            'UKR' => '🇺🇦', 'ARE' => '🇦🇪', 'USA' => '🇺🇸', 'URY' => '🇺🇾',
            'WAL' => '🏴󠁧󠁢󠁷󠁬󠁳󠁿', 'YUG' => '🇷🇸', 'COD' => '🇨🇩',
        ];

        return $flags[$this->team_code] ?? '🏳';
    }

    public function getFlagImgAttribute(): string
    {
        // Map ISO alpha-3 team codes to ISO 3166-1 alpha-2 for flagcdn.com
        $iso = [
            'DZA' => 'dz', 'AGO' => 'ao', 'ARG' => 'ar', 'AUS' => 'au',
            'AUT' => 'at', 'BEL' => 'be', 'BOL' => 'bo', 'BIH' => 'ba',
            'BRA' => 'br', 'BGR' => 'bg', 'CMR' => 'cm', 'CAN' => 'ca',
            'CHL' => 'cl', 'CHN' => 'cn', 'TWN' => 'tw', 'COL' => 'co',
            'CRI' => 'cr', 'HRV' => 'hr', 'CUB' => 'cu', 'CZE' => 'cz',
            'CSK' => 'cz', 'DNK' => 'dk', 'IDN' => 'id', 'DDR' => 'de',
            'ECU' => 'ec', 'EGY' => 'eg', 'SLV' => 'sv', 'ENG' => 'gb-eng',
            'FRA' => 'fr', 'DEU' => 'de', 'GHA' => 'gh', 'GRC' => 'gr',
            'GNQ' => 'gq', 'HTI' => 'ht', 'HND' => 'hn', 'HUN' => 'hu',
            'ISL' => 'is', 'IRN' => 'ir', 'IRQ' => 'iq', 'IRL' => 'ie',
            'ISR' => 'il', 'ITA' => 'it', 'CIV' => 'ci', 'JAM' => 'jm',
            'JPN' => 'jp', 'KWT' => 'kw', 'MEX' => 'mx', 'MAR' => 'ma',
            'NLD' => 'nl', 'NZL' => 'nz', 'NGA' => 'ng', 'PRK' => 'kp',
            'NIR' => 'gb-nir', 'NOR' => 'no', 'PAN' => 'pa', 'PRY' => 'py',
            'PER' => 'pe', 'POL' => 'pl', 'PRT' => 'pt', 'QAT' => 'qa',
            'IRL' => 'ie', 'ROU' => 'ro', 'RUS' => 'ru', 'SAU' => 'sa',
            'SCO' => 'gb-sct', 'SEN' => 'sn', 'SRB' => 'rs', 'SCG' => 'rs',
            'SVK' => 'sk', 'SVN' => 'si', 'ZAF' => 'za', 'KOR' => 'kr',
            'SUN' => 'ru', 'ESP' => 'es', 'SWE' => 'se', 'CHE' => 'ch',
            'THA' => 'th', 'TGO' => 'tg', 'TTO' => 'tt', 'TUN' => 'tn',
            'TUR' => 'tr', 'UKR' => 'ua', 'ARE' => 'ae', 'USA' => 'us',
            'URY' => 'uy', 'WAL' => 'gb-wls', 'YUG' => 'rs', 'COD' => 'cd',
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
