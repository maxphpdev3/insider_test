<?php

namespace App\Services;


use App\Models\Calendar;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\Team;

class SeasonService
{
    /**
     * League model
     *
     * @var League
     */
    protected $league;

    /**
     * Team class
     *
     * @var Team
     */
    protected $team;

    /**
     * League team class
     *
     * @var LeagueTeam
     */
    protected $leagueTeam;

    /**
     * Calendar class
     *
     * @var Calendar
     */
    protected $calendar;

    /**
     * Constructor for season service
     *
     * @param League $league
     */
    public function __construct(League $league)
    {
        $this->league = $league;
        $this->team = Team::class;
        $this->leagueTeam = LeagueTeam::class;
        $this->calendar = Calendar::class;
    }

    /**
     * Start new season with teams and schedule
     */
    public function startNewSeason()
    {
        $this->updateLeagueSeason();
        $this->addTeamsIntoLeague();
        $this->generateCalendar();
    }

    /**
     * Recalculate league table end set next week
     */
    public function weekFinished($nextWeek)
    {
        $this->recalculateCalendar($nextWeek);
        $this->league->update(['last_week' => $nextWeek]);
    }

    /**
     * Update league season and reset week
     */
    protected function updateLeagueSeason()
    {
        $this->league->season = $this->league->season + 1;
        $this->league->last_week = 0;
        $this->league->save();
    }

    /**
     * Insert teams into league table
     */
    protected function addTeamsIntoLeague()
    {
        $teams = $this->team::all();
        $withoutLast = ($teams->count() % 2 !== 0);

        foreach ($teams as $index => $team) {
            if ($withoutLast && $index === $teams->count() - 1) {
                break;
            }
            $leagueTeam = new $this->leagueTeam([
                'season' => $this->league->season,
                'team_id' => $team->id
            ]);
            $this->league->teams()->save($leagueTeam);
        }
    }

    /**
     * Generate calendar for season with empty result
     */
    protected function generateCalendar()
    {
        $teams = $this->league->getTeams()->pluck('team_id')->toArray();
        shuffle($teams);

        $matches = [];
        $weeks = [];

        // create schedule without match week number
        $teamsCount = count($teams);
        for ($i = 0; $i < $teamsCount; $i++) {
            $matches[$i] = [];
            for ($j = 0; $j < $teamsCount; $j++) {
                $matches[$i][$j] = ($i === $j) ? -1 : null;
            }
        }

        for ($w = 1, $full = false; !$full; $w++) {
            if (!isset($weeks[$w])) {
                $weeks[$w] = [];
            }
            $full = true;
            for ($i = 0; $i < $teamsCount; $i++) {
                for ($j = 0; $j < $teamsCount; $j++) {
                    if ($i == $j) {
                        continue;
                    }
                    // set match week number into schedule
                    if (is_null($matches[$i][$j])) {
                        if (!isset($weeks[$w][$i]) && !isset($weeks[$w][$j])) {
                            $matches[$i][$j] = $w;
                            $weeks[$w][$i] = true;
                            $weeks[$w][$j] = true;

                            $leagueTeam = new $this->calendar([
                                'season' => $this->league->season,
                                'home_team_id' => $teams[$i],
                                'away_team_id' => $teams[$j],
                                'match_week' => $w
                            ]);
                            $this->league->calendar()->save($leagueTeam);
                        } else {
                            $full = false;
                        }
                    }
                }
            }
        }
    }

    /**
     * Update league table with week`s result
     *
     * @param int $nextWeek
     */
    protected function recalculateCalendar(int $nextWeek)
    {
        $matches = $this->league->getMatchResultByWeekAndSeason($nextWeek);
        foreach ($matches as $match) {
            $homeTeam = $this->league->getTeamById($match->home_team_id);
            $result = $match->result;
            $isHomeWon = ($result->home_club_goals > $result->away_club_goals);
            $isDrawn = ($result->home_club_goals === $result->away_club_goals);
            $homeTeam->update([
                'points' => $homeTeam->points + ($isHomeWon ? 3 : ($isDrawn ? 1 : 0)),
                'played' => $homeTeam->played + 1,
                'won' => $homeTeam->won + ($isHomeWon ? 1 : 0),
                'drawn' => $homeTeam->drawn + ($isDrawn ? 1 : 0),
                'lost' => $homeTeam->lost + ((!$isHomeWon && !$isDrawn) ? 1 : 0),
                'goals_for' => $homeTeam->goals_for + $result->home_club_goals,
                'goals_against' => $homeTeam->goals_against + $result->away_club_goals
            ]);
            $awayTeam = $this->league->getTeamById($match->away_team_id);
            $awayTeam->update([
                'points' => $awayTeam->points + ($isHomeWon ? 0 : ($isDrawn ? 1 : 3)),
                'played' => $awayTeam->played + 1,
                'won' => $awayTeam->won + ($isHomeWon ? 0 : ($isDrawn ? 0 : 1)),
                'drawn' => $awayTeam->drawn + ($isDrawn ? 1 : 0),
                'lost' => $awayTeam->lost + ($isHomeWon ? 1 : 0),
                'goals_for' => $awayTeam->goals_for + $result->away_club_goals,
                'goals_against' => $awayTeam->goals_against + $result->home_club_goals
            ]);
        }
    }
}
