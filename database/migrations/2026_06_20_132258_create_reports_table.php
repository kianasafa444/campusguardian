<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id', 20)->unique();
            $table->foreignId('incident_category_id')->constrained('incident_categories')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('severity', 20)->default('Medium');
            $table->text('description');
            $table->string('location', 255)->nullable();
            $table->dateTime('incident_date')->nullable();
            $table->text('voice_to_text_raw')->nullable();
            $table->string('voice_file_path', 255)->nullable();
            $table->string('status', 20)->default('Submitted');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('student_verification_id')->nullable()->constrained('student_verifications')->nullOnDelete()->cascadeOnUpdate();
            $table->string('ip_address_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('incident_category_id');
            $table->index('status');
            $table->index('severity');
            $table->index('assigned_to');
            $table->index('student_verification_id');
            $table->index('submitted_at');
            $table->index('ip_address_hash');
            $table->index(['status', 'severity']);
            $table->index(['status', 'assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
