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
        Schema::create('compliance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('compliance_reports')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // created, submitted, approved, rejected
            $table->text('comments')->nullable();
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamps();

            $table->index(['report_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_audit_logs');
    }
};
