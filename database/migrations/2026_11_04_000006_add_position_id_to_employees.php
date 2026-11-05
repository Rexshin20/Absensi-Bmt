<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->after('name')->constrained()->nullOnDelete();
            $table->dropColumn('position');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('position', 100)->nullable()->after('name');
            $table->dropConstrainedForeignId('position_id');
        });
    }
};
