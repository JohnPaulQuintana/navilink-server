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
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('email')->index();

            // hashed OTP (never store plain OTP)
            $table->string('otp');

            // expiry time (5 minutes in your logic)
            $table->timestamp('expires_at')->index();

            // optional (you can keep or remove)
            $table->timestamp('used_at')->nullable();

            $table->index(['user_id', 'created_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
