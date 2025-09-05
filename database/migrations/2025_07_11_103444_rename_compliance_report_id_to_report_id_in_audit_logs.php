<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('compliance_audit_logs', function (Blueprint $table) {
            // Check if the old column exists before renaming
            if (Schema::hasColumn('compliance_audit_logs', 'compliance_report_id')) {
                $table->renameColumn('compliance_report_id', 'report_id');
            } else if (!Schema::hasColumn('compliance_audit_logs', 'report_id')) {
                // Add the column if it doesn't exist
                $table->foreignId('report_id')->constrained('compliance_reports')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('compliance_audit_logs', function (Blueprint $table) {
            $table->renameColumn('report_id', 'compliance_report_id');
        });
    }
};
