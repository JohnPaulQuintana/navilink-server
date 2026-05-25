<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // system flag
            $table->boolean('is_system')->default(false)->after('icon');

            // visibility (public / private)
            $table->enum('published', ['public', 'private'])
                ->default('private')
                ->after('is_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['is_system', 'published']);
        });
    }
};
