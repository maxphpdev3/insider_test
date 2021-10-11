<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Services\SeasonService;
use App\Services\SimulationService;

class LeagueTeamController extends Controller
{
    public function get($code)
    {
        try {
            $league = (new League())->getByCode($code);
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

    public function start_season($code)
    {
        try {
            $league = (new League())->getByCode($code);
            $seasonService = new SeasonService($league);
            $seasonService->startNewSeason();
            return response()->json([
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function generate_next_week($code)
    {
        try {
            $league = (new League())->getByCode($code);
            $seasonService = new SimulationService($league);
            $seasonService->generateNextWeek();
            return response()->json([
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function generate_all($code)
    {
        try {
            $league = (new League())->getByCode($code);
            $seasonService = new SimulationService($league);
            $seasonService->generateAll();
            return response()->json([
                'status' => 'success',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function last_week($code)
    {
        try {
            $league = (new League())->getByCode($code);
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

    public function results($code)
    {
        try {
            $league = (new League())->getByCode($code);
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
