<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentsSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'location_id' => 1,
                'description' => 'Finance and accounting department',
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'location_id' => 1,
                'description' => 'Human resources department',
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'location_id' => 1,
                'description' => 'IT support and development',
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'location_id' => 2,
                'description' => 'Business operations',
            ],
            [
                'name' => 'Compliance',
                'code' => 'COMP',
                'location_id' => 1,
                'description' => 'Regulatory compliance',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
