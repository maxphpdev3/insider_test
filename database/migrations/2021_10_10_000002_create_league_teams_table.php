<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeagueTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('league_id');
            $table->smallInteger('season')->default(1);
            $table->unsignedBigInteger('team_id');
            $table->smallInteger('points')->default(0);
            $table->smallInteger('played')->default(0);
            $table->smallInteger('won')->default(0);
            $table->smallInteger('drawn')->default(0);
            $table->smallInteger('lost')->default(0);
            $table->smallInteger('goals_for')->default(0);
            $table->smallInteger('goals_against')->default(0);
            $table->timestamps();

            $table->index(['league_id', 'season']);

            $table->foreign('league_id')
                ->references('id')
                ->on('leagues')
                ->onDelete('cascade');
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
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
        Schema::dropIfExists('league_teams');
    }
}
