<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('tournament_id');
            $table->string('award_name');
            $table->string('player_id')->nullable();
            $table->string('team_id')->nullable();
            $table->foreign('tournament_id')->references('tournament_id')->on('tournaments');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
