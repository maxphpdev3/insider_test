<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class League extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'code',
        'name',
        'season',
        'last_week'
    ];

    /**
     * Get league by code
     *
     * @param  string  $code
     * @return self|null
     */
    public function getByCode(string $code): self
    {
        return self::where('code', $code)->first();
    }

    /**
     * Get all teams from the league.
     *
     * @return HasMany
     */
    public function teams(): HasMany
    {
        return $this->hasMany(LeagueTeam::class, 'league_id', 'id');
    }

    /**
     * Get all schedule for the league.
     *
     * @return HasMany
     */
    public function calendar(): HasMany
    {
        return $this->hasMany(Calendar::class, 'league_id', 'id');
    }

    /**
     * Get table for the league.
     *
     * @return Collection
     */
    public function getTeams(): Collection
    {
        return $this
            ->teams()
            ->select([
                'id',
                'league_id',
                'team_id',
                'points',
                'played',
                'won',
                'drawn',
                'lost',
                'goals_for',
                'goals_against'
            ])
            ->with('team:id,code,name')
            ->where('season', $this->season)
            ->orderBy('points', 'desc')
            ->orderBy('goals_for', 'desc')
            ->get();
    }

    /**
     * Get all matches with result by week and season
     *
     * @param  int  $week
     * @param  int|null  $season
     * @return Collection
     */
    public function getMatchResultByWeekAndSeason(int $week, int $season = null): Collection
    {
        return $this
            ->calendar()
            ->where('season', $season ?? $this->season)
            ->where('match_week', $week)
            ->with('homeTeam:id,name')
            ->with('awayTeam:id,name')
            ->with('result:id,home_club_goals,away_club_goals')
            ->get();
    }
}
