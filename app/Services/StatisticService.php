<?php

namespace App\Services;


use App\Models\LeagueTeam;
use App\Models\TeamStatistic;
use Illuminate\Support\Collection;

class StatisticService
{
    /**
     * League model
     *
     * @var LeagueTeam
     */
    protected $leagueTeam;

    /**
     * Statistic class
     *
     * @var TeamStatistic
     */
    protected $teamStatistic;

    /**
     * Constructor for statistic service
     *
     * @param LeagueTeam $leagueTeam
     */
    public function __construct(LeagueTeam $leagueTeam)
    {
        $this->leagueTeam = $leagueTeam;
        $this->teamStatistic = TeamStatistic::class;
    }

    /**
     * Create empty statistic based on league teams
     */
    public function init()
    {
        if (!$this->leagueTeam->statistic) {
            $this->leagueTeam->statistic()->save(new $this->teamStatistic([]));
        }
    }

    /**
     * Recalculate teams statistic
     *
     * @param Collection $teams
     */
    public function recalculate(Collection $teams)
    {
        $teams = $teams->sortBy('points');
        $maxPoints = $teams->max('points');
        $teamsCount = $teams->count();
        $firstHalf = ($teamsCount - 1);
        $totalPrediction = 0;
        $counter = 0;
        foreach ($teams as $team) {
            $counter++;
            $pointsToFirst = $maxPoints - $team->points;
            if ($counter === $teamsCount) {
                // last team gets rest of predictions
                $prediction = 100 - $totalPrediction;
            } elseif ($pointsToFirst > (($teamsCount - 1) * 2 - $team->played) * 3) {
                // if team can not reach first place even if win all matches
                $prediction = 0;
            } elseif ($pointsToFirst === 0) {
                // all teams with the same points divided equally
                $prediction = round((100 - $totalPrediction) / ($teamsCount - ($counter - 1)));
            } else {
                // prediction basics on points and haw many matches have passed
                $prediction = 100 / $teamsCount - $pointsToFirst * ($team->played <= $firstHalf ? 1 : 2);
            }
            if ($prediction < 0)
                $prediction = 0;

            $team->statistic->update([
                'points_to_first' => $pointsToFirst,
                'avg_won' => (int) $team->won / $team->played * 100,
                'avg_goals_for' => round($team->goals_for / $team->played),
                'avg_goals_against' => round($team->goals_against / $team->played),
                'prediction' => $prediction
            ]);
            $totalPrediction += $prediction;
        }
    }
}
