<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_id')->unique();
            $table->foreignId('template_id')->constrained('compliance_templates')->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->date('due_date');
            $table->date('submitted_at')->nullable();
            $table->date('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('data'); // Stores all the field values
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('compliance_reports');
    }
};
