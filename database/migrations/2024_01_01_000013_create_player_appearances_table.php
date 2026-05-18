<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_appearances', function (Blueprint $table) {
            $table->id();
            $table->string('tournament_id');
            $table->string('match_id');
            $table->string('team_id');
            $table->string('player_id');
            $table->boolean('starter')->default(false);
            $table->boolean('substitute')->default(false);
            $table->integer('minutes_played')->nullable();
            $table->foreign('match_id')->references('match_id')->on('matches');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_appearances');
    }
};
