<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamStatistic extends Model
{
    use HasFactory;

    protected $table = 'team_statistic';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'points_to_first',
        'avg_won',
        'avg_goals_for',
        'avg_goals_against',
        'prediction'
    ];

    /**
     * Get a league team.
     *
     * @return BelongsTo
     */
    public function leagueTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class, 'id', 'league_team_id');
    }
}
