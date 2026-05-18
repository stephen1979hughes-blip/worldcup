<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stadiums', function (Blueprint $table) {
            $table->string('stadium_id')->primary();
            $table->string('stadium_name');
            $table->string('city_name');
            $table->string('country_name');
            $table->integer('capacity')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stadiums');
    }
};
