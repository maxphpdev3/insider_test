<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Calendar extends Model
{
    use HasFactory;

    protected $table = 'calendar';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'season',
        'home_team_id',
        'away_team_id',
        'match_week',
    ];

    /**
     * Get a league.
     *
     * @return BelongsTo
     */
    public function leagues(): BelongsTo
    {
        return $this->belongsTo(League::class, 'id', 'league_id');
    }

    /**
     * Get a home team.
     *
     * @return BelongsTo
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id', 'id');
    }

    /**
     * Get an away team.
     *
     * @return BelongsTo
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id', 'id');
    }

    /**
     * Get a match result with details.
     *
     * @return HasOne
     */
    public function result(): HasOne
    {
        return $this->hasOne(MatchResult::class, 'id', 'result_id');
    }
}
