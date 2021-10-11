<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = [
            'man_united' => 'Manchester United',
            'man_city' => 'Manchester City',
            'chelsea' => 'Chelsea',
            'liverpool' => 'Liverpool',
        ];

        foreach ($teams as $code => $name) {
            Team::updateOrCreate(
                ['code' => $code],
                ['name' => $name]
            );
        }
    }
}
