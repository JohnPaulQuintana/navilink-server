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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            // user who submitted feedback
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // category of feedback (UI, feature, bug, etc.)
            $table->string('category')->index();

            // rating (1 to 5)
            $table->unsignedTinyInteger('rating');

            // feedback message
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
