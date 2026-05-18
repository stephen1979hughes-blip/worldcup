<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->string('player_id')->primary();
            $table->string('given_name')->nullable();
            $table->string('family_name');
            $table->string('team_id');
            $table->integer('birth_year')->nullable();
            $table->string('goal_keeper')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
