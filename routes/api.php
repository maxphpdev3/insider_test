<?php

use App\Http\Controllers\LeagueTeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'league-table'], function () {
        Route::get('/{code}', [LeagueTeamController::class, 'get']);
        Route::post('/{code}/start-season', [LeagueTeamController::class, 'start_season']);
        Route::post('/{code}/play-next', [LeagueTeamController::class, 'play_next']);
        Route::post('/{code}/play-all', [LeagueTeamController::class, 'play_all']);
        Route::get('/{code}/last_week', [LeagueTeamController::class, 'last_week']);
        Route::get('/{code}/prediction', [LeagueTeamController::class, 'prediction']);
    });
});
