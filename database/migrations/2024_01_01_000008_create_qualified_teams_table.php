<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qualified_teams', function (Blueprint $table) {
            $table->id();
            $table->string('tournament_id');
            $table->string('team_id');
            $table->string('group_name')->nullable();
            $table->string('group_stage_result')->nullable();
            $table->string('final_position')->nullable();
            $table->integer('matches_played')->default(0);
            $table->integer('matches_won')->default(0);
            $table->integer('matches_drawn')->default(0);
            $table->integer('matches_lost')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->integer('points')->default(0);
            $table->foreign('tournament_id')->references('tournament_id')->on('tournaments');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qualified_teams');
    }
};
