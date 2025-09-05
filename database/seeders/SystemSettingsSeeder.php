<?php

namespace Database\Seeders;

use App\Helpers\SettingsHelper;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'app.name',
                'value' => 'DMS System',
                'description' => 'Application display name'
            ],
            [
                'key' => 'document.retention_days',
                'value' => 365,
                'description' => 'How long to keep documents before automatic deletion'
            ],
            [
                'key' => 'notifications.email',
                'value' => true,
                'description' => 'Enable email notifications system-wide'
            ]
        ];

        foreach ($settings as $setting) {
            SettingsHelper::set(
                $setting['key'],
                $setting['value'],
                $setting['description']
            );
        }
    }
}
