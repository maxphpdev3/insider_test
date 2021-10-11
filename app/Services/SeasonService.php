<?php

namespace App\Services;


use App\Models\Calendar;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\Team;

class SeasonService
{
    protected $league;
    protected $team;
    protected $leagueTeam;
    protected $calendar;

    public function __construct(League $league)
    {
        $this->league = $league;
        $this->team = Team::class;
        $this->leagueTeam = LeagueTeam::class;
        $this->calendar = Calendar::class;
    }

    public function startNewSeason()
    {
        $this->updateLeagueSeason();
        $this->addTeamsIntoLeague();
        $this->generateCalendar();
    }

    protected function updateLeagueSeason()
    {
        $this->league->season = $this->league->season + 1;
        $this->league->last_week = 0;
        $this->league->save();
    }

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

    protected function generateCalendar()
    {
        $teams = $this->league->getTeams()->pluck('team_id')->toArray();
        shuffle($teams);

        $matches = [];
        $weeks = [];

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
}
