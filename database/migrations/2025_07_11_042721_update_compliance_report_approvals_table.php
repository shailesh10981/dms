<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('compliance_report_approvals', function (Blueprint $table) {
            // Rename the column if it exists
            if (Schema::hasColumn('compliance_report_approvals', 'compliance_report_id')) {
                $table->renameColumn('compliance_report_id', 'report_id');
            }

            // Or add the correct column if it doesn't exist
            if (!Schema::hasColumn('compliance_report_approvals', 'report_id')) {
                $table->foreignId('report_id')->constrained('compliance_reports')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('compliance_report_approvals', function (Blueprint $table) {
            // For rollback
            $table->renameColumn('report_id', 'compliance_report_id');
        });
    }
};
