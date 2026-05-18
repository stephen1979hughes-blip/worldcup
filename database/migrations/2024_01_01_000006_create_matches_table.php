<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->string('match_id')->primary();
            $table->string('tournament_id');
            $table->string('stage_name');
            $table->string('group_name')->nullable();
            $table->integer('match_number')->default(0);
            $table->date('match_date')->nullable();
            $table->string('stadium_id')->nullable();
            $table->string('home_team_id');
            $table->string('away_team_id');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->integer('home_score_et')->nullable();
            $table->integer('away_score_et')->nullable();
            $table->boolean('penalties')->default(false);
            $table->integer('home_score_pen')->nullable();
            $table->integer('away_score_pen')->nullable();
            $table->string('result')->nullable();
            $table->integer('attendance')->nullable();
            $table->foreign('tournament_id')->references('tournament_id')->on('tournaments');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
