<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_appointments', function (Blueprint $table) {
            $table->id();
            $table->string('tournament_id');
            $table->string('team_id');
            $table->string('manager_id');
            $table->foreign('tournament_id')->references('tournament_id')->on('tournaments');
            $table->foreign('manager_id')->references('manager_id')->on('managers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_appointments');
    }
};
