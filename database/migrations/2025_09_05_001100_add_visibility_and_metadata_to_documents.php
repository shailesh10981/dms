<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'visibility')) {
                $table->enum('visibility', ['Private', 'Public', 'Publish'])->default('Private')->after('status');
            }
            if (!Schema::hasColumn('documents', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('uploaded_by');
            }
            if (!Schema::hasColumn('documents', 'modified_by')) {
                $table->unsignedBigInteger('modified_by')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('documents', 'created_date')) {
                $table->date('created_date')->nullable()->after('modified_by');
            }
            if (!Schema::hasColumn('documents', 'modified_date')) {
                $table->date('modified_date')->nullable()->after('created_date');
            }
            if (!Schema::hasColumn('documents', 'workflow_definition')) {
                $table->json('workflow_definition')->nullable()->after('modified_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'visibility')) $table->dropColumn('visibility');
            if (Schema::hasColumn('documents', 'created_by')) $table->dropColumn('created_by');
            if (Schema::hasColumn('documents', 'modified_by')) $table->dropColumn('modified_by');
            if (Schema::hasColumn('documents', 'created_date')) $table->dropColumn('created_date');
            if (Schema::hasColumn('documents', 'modified_date')) $table->dropColumn('modified_date');
            if (Schema::hasColumn('documents', 'workflow_definition')) $table->dropColumn('workflow_definition');
        });
    }
};
