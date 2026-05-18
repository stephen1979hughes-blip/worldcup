<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->string('tournament_id')->primary();
            $table->integer('year');
            $table->string('host_country');
            $table->string('host_continent')->nullable();
            $table->string('winner_team_id')->nullable();
            $table->string('runner_up_team_id')->nullable();
            $table->string('third_place_team_id')->nullable();
            $table->string('fourth_place_team_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('num_teams')->default(0);
            $table->integer('num_matches')->default(0);
            $table->integer('num_goals')->default(0);
            $table->string('format')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
