<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('squads', function (Blueprint $table) {
            $table->id();
            $table->string('tournament_id');
            $table->string('team_id');
            $table->string('player_id');
            $table->string('position_name')->nullable();
            $table->string('position_code')->nullable();
            $table->integer('shirt_number')->nullable();
            $table->foreign('tournament_id')->references('tournament_id')->on('tournaments');
            $table->foreign('player_id')->references('player_id')->on('players');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('squads');
    }
};
