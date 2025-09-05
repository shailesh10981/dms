<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@dms.com',
            'password' => Hash::make('password'),
            'department_id' => 5, // Compliance
        ]);
        $admin->assignRole('admin');

        // Manager User
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@dms.com',
            'password' => Hash::make('password'),
            'department_id' => 1, // Finance
        ]);
        $manager->assignRole('manager');



        // Compliance Officer
        $complianceOfficer = User::create([
            'name' => 'Compliance Officer',
            'email' => 'compliance@dms.com',
            'password' => Hash::make('password'),
            'department_id' => 5, // Compliance
        ]);
        $complianceOfficer->assignRole('compliance_officer');

        // Regular Users
        $users = [
            ['name' => 'Finance User 1', 'email' => 'finance1@dms.com', 'department_id' => 1],
            ['name' => 'Finance User 2', 'email' => 'finance2@dms.com', 'department_id' => 1],
            ['name' => 'HR User 1', 'email' => 'hr1@dms.com', 'department_id' => 2],
            ['name' => 'IT User 1', 'email' => 'it1@dms.com', 'department_id' => 3],
            ['name' => 'Operations User 1', 'email' => 'ops1@dms.com', 'department_id' => 4],
            ['name' => 'Auditor User', 'email' => 'auditor@dms.com', 'department_id' => 5],
        ];

        foreach ($users as $user) {
            $newUser = User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'department_id' => $user['department_id'],
            ]);
            $newUser->assignRole('user');
        }

        // Assign auditor role to the auditor user
        $auditor = User::where('email', 'auditor@dms.com')->first();
        $auditor->assignRole('auditor');
    }
}
