<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_expiry_notified')->default(false);
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['project_id']);
            $table->dropColumn(['location_id', 'project_id', 'expiry_date', 'is_expiry_notified', 'deleted_at']);
        });
    }
};
