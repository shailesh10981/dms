<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            [
                'name' => 'Headquarters',
                'code' => 'HQ',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10001',
                'phone' => '+1 212-555-1234',
                'email' => 'hq@company.com',
                'description' => 'Corporate headquarters',
                'is_active' => true,
            ],
            [
                'name' => 'Regional Office - West',
                'code' => 'ROW',
                'address' => '456 Sunset Blvd',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'country' => 'USA',
                'postal_code' => '90001',
                'phone' => '+1 213-555-5678',
                'email' => 'west@company.com',
                'description' => 'West coast regional office',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
