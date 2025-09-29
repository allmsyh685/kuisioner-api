<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('questions', 'options')) {
            // Prefer dropping the legacy JSON column to match current schema
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('options');
            });
        }
    }

    public function down(): void
    {
        // Restore as nullable JSON to avoid blocking inserts if rolled back
        if (!Schema::hasColumn('questions', 'options')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->json('options')->nullable();
            });
        }
    }
};





