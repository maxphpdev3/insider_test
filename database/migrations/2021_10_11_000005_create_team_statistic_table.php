<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_statistic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('league_team_id')->index();
            $table->unsignedSmallInteger('points_to_first')->default(0);
            $table->unsignedSmallInteger('avg_won')->default(0);
            $table->unsignedSmallInteger('avg_goals_for')->default(0);
            $table->unsignedSmallInteger('avg_goals_against')->default(0);
            $table->unsignedSmallInteger('prediction')->default(0);
            $table->timestamps();

            $table->foreign('league_team_id')
                ->references('id')
                ->on('league_teams')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_statistic');
    }
}
