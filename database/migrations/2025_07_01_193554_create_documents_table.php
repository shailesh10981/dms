<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('draft'); // draft, pending_review, approved, rejected
            $table->unsignedBigInteger('current_approver_id')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('version')->default(1);
            $table->unsignedBigInteger('parent_id')->nullable()->constrained('documents');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
