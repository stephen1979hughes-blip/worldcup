<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->string('team_id')->primary();
            $table->string('team_name');
            $table->string('team_code', 10)->nullable();
            $table->string('confederation')->nullable();
            $table->string('region')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
