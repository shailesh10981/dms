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
        Schema::create('compliance_template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('compliance_templates')->cascadeOnDelete();
            $table->string('label');
            $table->string('field_type')->default('text'); // text, number, date, select, checkbox
            $table->text('options')->nullable(); // For select fields
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->json('validation_rules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_template_fields');
    }
};
