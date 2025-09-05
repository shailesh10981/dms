<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('profile_picture')->nullable()->after('address');
            $table->string('employee_id')->nullable()->after('profile_picture');
            $table->date('joining_date')->nullable()->after('employee_id');
            $table->date('birth_date')->nullable()->after('joining_date');
            $table->string('gender')->nullable()->after('birth_date');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'profile_picture',
                'employee_id',
                'joining_date',
                'birth_date',
                'gender'
            ]);
        });
    }
};
