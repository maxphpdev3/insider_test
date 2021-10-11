<?php

namespace App\Services;


use App\Models\Calendar;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\Team;

class SimulationService
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

    public function playNext()
    {

    }

    public function playAll()
    {

    }
}
