<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('booking_id')->primary();
            $table->string('tournament_id');
            $table->string('match_id');
            $table->string('team_id');
            $table->string('player_id')->nullable();
            $table->string('booking_type');
            $table->integer('minute')->nullable();
            $table->foreign('match_id')->references('match_id')->on('matches');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
