<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProjectsSeeder extends Seeder
{
    public function run()
    {
        $projects = [
            [
                'name' => 'Digital Transformation',
                'code' => 'DT2023',
                'description' => 'Company-wide digital transformation initiative',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9),
                'department_id' => 3, // IT
            ],
            [
                'name' => 'Regulatory Compliance',
                'code' => 'RC2023',
                'description' => 'Annual regulatory compliance updates',
                'start_date' => Carbon::now()->subMonth(),
                'end_date' => Carbon::now()->addMonths(2),
                'department_id' => 5, // Compliance
            ],
            [
                'name' => 'Financial Audit',
                'code' => 'FA2023',
                'description' => 'Year-end financial audit',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(30),
                'department_id' => 1, // Finance
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
