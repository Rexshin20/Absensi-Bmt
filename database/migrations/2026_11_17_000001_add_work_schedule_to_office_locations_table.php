<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_locations', function (Blueprint $table) {
            $table->json('work_days')->nullable()->after('radius_m');
            $table->string('work_start_time', 5)->nullable()->after('work_days');
        });
    }

    public function down(): void
    {
        Schema::table('office_locations', function (Blueprint $table) {
            $table->dropColumn(['work_days', 'work_start_time']);
        });
    }
};
