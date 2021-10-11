<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueTeam;
use App\Services\SeasonService;
use Illuminate\Http\Request;

class LeagueTeamController extends Controller
{
    public function get($code) {
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

    public function start_season($code) {
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

    public function play_next($code) {
        try {
            $league = (new League())->getByCode($code);
            $seasonService = new SeasonService($league);
            $seasonService->playNext();
            return response()->json([
                'status' => 'success',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function play_all($code) {
        try {
            $league = (new League())->getByCode($code);
            return response()->json([
                'status' => 'success',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function last_week($code) {
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

    public function prediction($code) {
        try {
            $league = (new League())->getByCode($code);
            return response()->json([
                'status' => 'success',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
