<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->index('player_id');
            $table->index('tournament_id');
            $table->index('team_id');
        });

        Schema::table('squads', function (Blueprint $table) {
            $table->index('player_id');
            $table->index('tournament_id');
            $table->index('team_id');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->index('tournament_id');
            $table->index('home_team_id');
            $table->index('away_team_id');
        });

        Schema::table('player_appearances', function (Blueprint $table) {
            $table->index('player_id');
            $table->index('tournament_id');
        });

        Schema::table('qualified_teams', function (Blueprint $table) {
            $table->index('tournament_id');
            $table->index('team_id');
        });
    }

    public function down(): void {}
};
