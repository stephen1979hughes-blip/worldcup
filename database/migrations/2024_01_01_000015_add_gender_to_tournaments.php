<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->string('gender', 6)->default('men')->after('tournament_id');
        });

        // Backfill from tournament_id naming pattern — women's WCs started 1991
        // and interleave with men's, so use the name stored during import.
        // Derive from year: women's years are 1991,1995,1999,2003,2007,2011,2015,2019
        $womensYears = [1991, 1995, 1999, 2003, 2007, 2011, 2015, 2019];
        DB::table('tournaments')
            ->whereIn('year', $womensYears)
            ->update(['gender' => 'women']);
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
