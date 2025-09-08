<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('risk_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('risk_reports', 'current_approver_id')) {
                $table->foreignId('current_approver_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('risk_reports', function (Blueprint $table) {
            if (Schema::hasColumn('risk_reports', 'current_approver_id')) {
                $table->dropConstrainedForeignId('current_approver_id');
            }
        });
    }
};
