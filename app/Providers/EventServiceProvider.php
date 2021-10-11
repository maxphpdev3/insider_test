<?php

namespace App\Providers;

use App\Models\League;
use App\Models\LeagueTeam;
use App\Services\StatisticService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        LeagueTeam::created(function ($leagueTeam) {
            // init statistic if new season created
            $statisticService = new StatisticService($leagueTeam);
            $statisticService->init();
        });

        League::updated(function ($league) {
            // recalculate statistic after week changed
            if ($league->last_week > $league->getOriginal('last_week')) {
                $statisticService = new StatisticService(new LeagueTeam());
                $statisticService->recalculate($league->getTeamsBySeason());
            }
        });
    }
}
