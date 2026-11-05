<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            'divisions' => 'departments_code_unique',
            'positions' => 'positions_code_unique',
            'leave_types' => 'leave_types_code_unique',
        ];

        foreach ($indexes as $tableName => $indexName) {
            Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }

    public function down(): void
    {
        foreach (['divisions', 'positions', 'leave_types'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('code', 50)->nullable()->unique()->after('name');
            });
        }
    }
};
