<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->string('goal_id')->primary();
            $table->string('tournament_id');
            $table->string('match_id');
            $table->string('team_id');
            $table->string('player_id')->nullable();
            $table->integer('minute')->nullable();
            $table->integer('minute_stoppage')->nullable();
            $table->string('goal_type')->default('goal');
            $table->boolean('penalty')->default(false);
            $table->boolean('own_goal')->default(false);
            $table->foreign('match_id')->references('match_id')->on('matches');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
