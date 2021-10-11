<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeagueTeam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'season',
        'team_id',
        'points',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against'
    ];

    /**
     * Get a league.
     *
     * @return BelongsTo
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class, 'id', 'league_id');
    }

    /**
     * Get a team.
     *
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    /**
     * Get team's statistic.
     *
     * @return HasOne
     */
    public function statistic(): HasOne
    {
        return $this->hasOne(TeamStatistic::class, 'league_team_id', 'id');
    }
}
