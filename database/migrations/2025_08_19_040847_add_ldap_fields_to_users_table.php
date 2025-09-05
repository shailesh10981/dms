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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ldap_dn')->nullable()->after('email');
            $table->string('ldap_username')->nullable()->after('ldap_dn');
            $table->string('department')->nullable()->after('ldap_username');
            $table->string('title')->nullable()->after('department');
            $table->boolean('is_ldap_user')->default(false)->after('title');
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ldap_dn', 'ldap_username', 'department', 'title', 'is_ldap_user']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
