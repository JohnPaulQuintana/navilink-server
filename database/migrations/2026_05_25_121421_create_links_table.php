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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            // User relationship
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Category relationship
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Link basic data
            $table->string('title')->nullable();
            $table->text('url');
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->text('favicon')->nullable();
            $table->text('domain')->nullable();

            $table->boolean('issynced')
                ->default(false);

            $table->string('platform')
                ->nullable();

            $table->string('safety_status')
                ->default('unknown');

            $table->timestamp('visited_date')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
