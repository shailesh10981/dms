<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('risk_reports', function (Blueprint $table) {
            $table->id();
            $table->string('risk_id')->unique();
            $table->enum('issue_type', ['operational', 'compliance', 'financial', 'security']);
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->json('data');
            $table->string('attachment_path')->nullable();
            $table->date('submitted_at')->nullable();
            $table->date('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('workflow_definition')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('risk_report_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_report_id')->constrained('risk_reports')->onDelete('cascade');
            $table->unsignedInteger('step_order');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comments')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
            $table->unique(['risk_report_id', 'step_order']);
        });

        Schema::create('risk_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_report_id')->constrained('risk_reports')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('action');
            $table->text('comments')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['risk_report_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_audit_logs');
        Schema::dropIfExists('risk_report_approvals');
        Schema::dropIfExists('risk_reports');
    }
};
