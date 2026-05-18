<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('managers', function (Blueprint $table) {
            $table->string('manager_id')->primary();
            $table->string('given_name')->nullable();
            $table->string('family_name');
            $table->string('team_id');
            $table->string('home_country')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
