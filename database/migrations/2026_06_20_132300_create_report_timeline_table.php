<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('previous_status', 20)->nullable();
            $table->string('new_status', 20)->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_admin_note')->default(false);
            $table->foreignId('action_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->index('report_id');
            $table->index('created_at');
            $table->index(['report_id', 'created_at']);
            $table->index('action_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_timeline');
    }
};
