<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->string('file_type', 50);
            $table->unsignedInteger('file_size');
            $table->string('mime_type', 100);
            $table->timestamps();

            $table->index('report_id');
            $table->index('file_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_evidences');
    }
};
