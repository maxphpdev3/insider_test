<?php

namespace App\Services;


use App\Models\Calendar;
use App\Models\League;
use App\Models\MatchResult;
use App\Models\TeamStatistic;

class SimulationService
{
    /**
     * League model
     *
     * @var League
     */
    protected $league;

    /**
     * Match result class
     *
     * @var MatchResult
     */
    protected $matchResult;

    /**
     * Season service instance
     *
     * @var SeasonService
     */
    protected $seasonService;

    /**
     * Default probabilities for goals
     *
     * @var array
     */
    const DEFAULT_PROBABILITY_SET = [
        0 => 0.15,
        1 => 0.4,
        2 => 0.25,
        3 => 0.1,
        4 => 0.06,
        5 => 0.022,
        6 => 0.01,
        7 => 0.005,
        8 => 0.002,
        9 => 0.001
    ];

    /**
     * Sum of probabilities for 5-9 goals
     *
     * @var float
     */
    const SUM_OF_STABLE_PROBABILITIES = 0.04;

    /**
     * Small prop for team who hosts
     *
     * @var float
     */
    const COEFFICIENT_HOME = 1.05;

    /**
     * Goals for coefficient
     *
     * @var float
     */
    const COEFFICIENT_GOALS_FOR = 0.05;

    /**
     * Goals against coefficient
     *
     * @var float
     */
    const COEFFICIENT_GOALS_AGAINST = 0.1;

    /**
     * Constructor for simulation service
     *
     * @param League $league
     */
    public function __construct(League $league)
    {
        $this->league = $league;
        $this->matchResult = MatchResult::class;
        $this->seasonService = new SeasonService($this->league);
    }

    /**
     * Generate results of next week
     */
    public function generateNextWeek()
    {
        $nextWeek = $this->league->last_week + 1;
        $matches = $this->league->getMatchResultByWeekAndSeason($nextWeek);
        foreach ($matches as $match) {
            $this->generateMatch($match);
        }
        $this->seasonService->weekFinished($nextWeek);
    }

    /**
     * Generate all results of season
     */
    public function generateAll()
    {
        $teamsCount = $this->league->getTeamsBySeason()->count();
        $weeks = ($teamsCount - 1) * 2;
        for ($i = 0; $i < $weeks; $i++) {
            $this->generateNextWeek();
        }
    }

    /**
     * Generate a match result
     *
     * @param Calendar $match
     */
    protected function generateMatch(Calendar $match)
    {
        if ($match->result_id)
            return;

        $homeTeam = $this->league->getTeamById($match->home_team_id);
        $awayTeam = $this->league->getTeamById($match->away_team_id);

        if ($this->league->last_week === 0) {
            // first week in league with equal probabilities
            $homeSet = self::DEFAULT_PROBABILITY_SET;
            $awaySet = self::DEFAULT_PROBABILITY_SET;
        } else {
            // get probabilities for goals for two teams
            $sets = $this->getProbabilitySets($homeTeam->statistic, $awayTeam->statistic);
            $homeSet = $sets['home'];
            $awaySet = $sets['away'];
        }

        $result = new $this->matchResult([
            'home_club_goals' => $this->getScore($homeSet),
            'away_club_goals' => $this->getScore($awaySet),
        ]);
        $result->save();
        $match->update(['result_id' => $result->id]);
    }

    /**
     * Get goal from the set of probabilities
     *
     * @param array $set
     * @param int $length
     * @return int
     */
    protected function getScore(array $set, int $length = 10000): int
    {
        $left = 0;
        foreach($set as $goal => $right) {
            $set[$goal] = $left + $right * $length;
            $left = $set[$goal];
        }
        $test = mt_rand(1, $length);
        $left = 1;
        foreach ($set as $goal => $right) {
            if ($test >= $left && $test <= $right) {
                return $goal;
            }
            $left = $right;
        }
        return 0;
    }

    /**
     * Get probabilities set based on statistic
     *
     * @param TeamStatistic $home
     * @param TeamStatistic $away
     * @return array
     */
    protected function getProbabilitySets(TeamStatistic $home, TeamStatistic $away): array
    {
        $pointsToFirstDiff = $home->points_to_first - $away->points_to_first;
        $moreGoalsFor = $home->avg_goals_for < $away->avg_goals_against;
        $lessGoalsAgainst = $home->avg_goals_against < $away->avg_goals_for;

        $coefficient = self::COEFFICIENT_HOME + $pointsToFirstDiff / 100 +
            ($moreGoalsFor ? self::COEFFICIENT_GOALS_FOR : -self::COEFFICIENT_GOALS_FOR) +
            ($lessGoalsAgainst ? self::COEFFICIENT_GOALS_AGAINST : -self::COEFFICIENT_GOALS_FOR);

        $homeSet = [];
        $homeProbabilitySum = 0;
        $awaySet = [];
        $awayProbabilitySum = 0;
        foreach (self::DEFAULT_PROBABILITY_SET as $goal => $probability) {
            if ($goal < 2) {
                $homeSet[$goal] = $probability / $coefficient;
                $homeProbabilitySum += $homeSet[$goal];
                $awaySet[$goal] = $probability * $coefficient;
                $awayProbabilitySum += $homeSet[$goal];
            } elseif ($goal < 5) {
                $homeSet[$goal] = $probability * $coefficient;
                $homeProbabilitySum += $homeSet[$goal];
                $awaySet[$goal] = $probability / $coefficient;
                $awayProbabilitySum += $homeSet[$goal];
            } elseif ($goal === 5) {
                $homeSet[$goal] = 100 - self::SUM_OF_STABLE_PROBABILITIES - $homeProbabilitySum;
                $homeSet[$goal] = 100 - self::SUM_OF_STABLE_PROBABILITIES - $awayProbabilitySum;
            } else {
                $homeSet[$goal] = $probability;
                $awaySet[$goal] = $probability;
            }
        }

        return [
            'home' => $homeSet,
            'away' => $awaySet,
        ];
    }
}
