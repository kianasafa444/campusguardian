<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->unique();
            $table->string('email_domain', 100);
            $table->string('contact_email', 255)->nullable();
            $table->string('verification_token', 64)->unique();
            $table->string('otp_hash', 255)->nullable();
            $table->tinyInteger('otp_attempts')->unsigned()->default(0);
            $table->tinyInteger('max_attempts')->unsigned()->default(5);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('ip_address_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('email_domain');
            $table->index('is_verified');
            $table->index('otp_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_verifications');
    }
};
