<?php

namespace Database\Seeders;

use App\Models\League;
use Illuminate\Database\Seeder;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        League::updateOrCreate(
            ['code' => 'premier_league'],
            [
                'name' => 'Premier League',
                'season' => 0,
            ]
        );
    }
}
