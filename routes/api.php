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
    Route::group(['prefix' => 'league-table/{code}'], function () {
        Route::get('/', [LeagueTeamController::class, 'get']);
        Route::post('/start-season', [LeagueTeamController::class, 'start_season']);
        Route::post('/generate-next', [LeagueTeamController::class, 'generate_next_week']);
        Route::post('/generate-all', [LeagueTeamController::class, 'generate_all']);
        Route::get('/last_week', [LeagueTeamController::class, 'last_week']);
        Route::get('/results', [LeagueTeamController::class, 'results']);
    });
});
