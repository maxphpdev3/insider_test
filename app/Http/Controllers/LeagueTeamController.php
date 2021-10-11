<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Services\SeasonService;
use App\Services\SimulationService;

class LeagueTeamController extends Controller
{
    /**
     * Get league
     *
     * Get information about league and teams
     *
     * @urlParam code integer required The code of the league.
     *
     * @response {
     *  "status": "success",
     *  "data": {
     *      "league": {
     *          "id": 1,
     *          "code": "premier_league",
     *          "last_week": 6,
     *          "name": "Premier League",
     *          "season": 8,
     *          "created_at": "2021-10-11T10:15:39.000000Z",
     *          "updated_at": "2021-10-11T10:50:32.000000Z"
     *      },
     *      "teams": [
     *          {
     *              "drawn": 2,
     *              "goals_against": 8,
     *              "goals_for": 13,
     *              "id": 21,
     *              "league_id": 1,
     *              "lost": 1,
     *              "played": 6,
     *              "points": 11,
     *              "statistic": {
     *                  "id": 21,
     *                  "league_team_id": 21,
     *                  "prediction": 100
     *              },
     *              "team": {
     *                  "id": 1,
     *                  "code": "man_united",
     *                  "name": "Manchester United"
     *              },
     *              "team_id": 1,
     *              "won": 3
     *          }
     *      ]
     *  }
     * }
     * @response status=500 scenario="internal server error" {"status": "error"}
     */
    public function get($code, League $league)
    {
        try {
            $league = $league->getByCode($code);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'league' => $league,
                    'teams' => $league->getTeams()
                ]
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Start new season
     *
     * @urlParam code integer required The code of the league.
     *
     * @response {
     *  "status": "success"
     * }
     * @response status=500 scenario="internal server error" {"status": "error"}
     */
    public function start_season($code, League $league)
    {
        try {
            $league = $league->getByCode($code);
            $seasonService = new SeasonService($league);
            $seasonService->startNewSeason();
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Generate result of next week
     *
     * @urlParam code integer required The code of the league.
     *
     * @response {
     *  "status": "success"
     * }
     * @response status=500 scenario="internal server error" {"status": "error"}
     */
    public function generate_next_week($code, League $league)
    {
        try {
            $league = $league->getByCode($code);
            $seasonService = new SimulationService($league);
            $seasonService->generateNextWeek();
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Generate all results
     *
     * @urlParam code integer required The code of the league.
     *
     * @response {
     *  "status": "success"
     * }
     * @response status=500 scenario="internal server error" {"status": "error"}
     */
    public function generate_all($code, League $league)
    {
        try {
            $league = $league->getByCode($code);
            $seasonService = new SimulationService($league);
            $seasonService->generateAll();
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Get last week results
     *
     * @urlParam code integer required The code of the league.
     *
     * @response {
     *  "status": "success",
     *  "data": {
     *      "last_week": 1,
     *      "results": [
     *          {
     *              "id": 73,
     *              "league_id": 1,
     *              "season": 9,
     *              "match_week": 1,
     *              "away_team_id": 1,
     *              "away_team": {
     *                  "id": 1,
     *                  "name": "Manchester United"
     *              },
     *              "home_team_id": 3,
     *              "home_team": {
     *                  "id": 3,
     *                  "name": "Chelsea"
     *              },
     *              "result_id": 47,
     *              "result": {
     *                  "id": 47,
     *                  "home_club_goals": 1,
     *                  "away_club_goals": 5
     *              },
     *              "created_at": "2021-10-11T12:52:57.000000Z",
     *              "updated_at": "2021-10-11T12:52:59.000000Z"
     *          }
     *      ]
     *  }
     * }
     * @response status=500 scenario="internal server error" {"status": "error"}
     */
    public function last_week($code, League $league)
    {
        try {
            $league = $league->getByCode($code);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'last_week' => $league->last_week,
                    'results' => $league->getMatchResultByWeekAndSeason($league->last_week)
                ]
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }


    /**
     * Get all matches result
     *
     * @urlParam code integer required The code of the league.
     *
     * @response {
     *  "status": "success",
     *  "data": {
     *      "1": [
     *          {
     *              "id": 73,
     *              "league_id": 1,
     *              "season": 9,
     *              "match_week": 1,
     *              "away_team_id": 1,
     *              "away_team": {
     *                  "id": 1,
     *                  "name": "Manchester United"
     *              },
     *              "home_team_id": 3,
     *              "home_team": {
     *                  "id": 3,
     *                  "name": "Chelsea"
     *              },
     *              "result_id": 47,
     *              "result": {
     *                  "id": 47,
     *                  "home_club_goals": 1,
     *                  "away_club_goals": 5
     *              },
     *              "created_at": "2021-10-11T12:52:57.000000Z",
     *              "updated_at": "2021-10-11T12:52:59.000000Z"
     *          }
     *      ]
     *  }
     * }
     * @response status=500 scenario="internal server error" {"status": "error"}
     */
    public function results($code, League $league)
    {
        try {
            $league = $league->getByCode($code);
            $res = [];
            foreach ($league->getMatchResult() as $result) {
                $res[$result->match_week][] = $result;
            }
            return response()->json([
                'status' => 'success',
                'data' => $res
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
