<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('league_id')->index();
            $table->smallInteger('season')->default(1);
            $table->unsignedBigInteger('home_team_id');
            $table->unsignedBigInteger('away_team_id');
            $table->smallInteger('match_week');
            $table->unsignedBigInteger('result_id')->nullable();
            $table->timestamps();

            $table->foreign('league_id')
                ->references('id')
                ->on('leagues')
                ->onDelete('cascade');
            $table->foreign('home_team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');
            $table->foreign('away_team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');
            $table->foreign('result_id')
                ->references('id')
                ->on('match_results')
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
        Schema::dropIfExists('calendar');
    }
}
