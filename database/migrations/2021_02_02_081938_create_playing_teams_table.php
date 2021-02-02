<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayingTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playing_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_squad_id')->references('id')->on('match_squads');
            $table->foreignId('opponent_playing_team_id')->references('id')->on('playing_teams');
            $table->string('playing_squad')->nullable();
            $table->timestamp('end_at', $precision = 0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playing_teams');
    }
}
