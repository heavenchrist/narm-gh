<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Administrator;
use App\Models\Region;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Administrator::updateOrCreate([
            'email' =>'system@narmghana.org',
        ],
        [
            'name' =>'Super Admin',
            'password' =>bcrypt('password'),

        ]);
        $regions = [
    ['name' => 'Ahafo Region', 'status' => true],
    ['name' => 'Ashanti Region', 'status' => true],
    ['name' => 'Bono Region', 'status' => true],
    ['name' => 'Bono East Region', 'status' => true],
    ['name' => 'Central Region', 'status' => true],
    ['name' => 'Eastern Region', 'status' => true],
    ['name' => 'Greater Accra Region', 'status' => true],
    ['name' => 'North East Region', 'status' => true],
    ['name' => 'Northern Region', 'status' => true],
    ['name' => 'Oti Region', 'status' => true],
    ['name' => 'Savannah Region', 'status' => true],
    ['name' => 'Upper East Region', 'status' => true],
    ['name' => 'Upper West Region', 'status' => true],
    ['name' => 'Volta Region', 'status' => true],
    ['name' => 'Western Region', 'status' => true],
    ['name' => 'Western North Region', 'status' => true],
];
foreach ($regions as $region) {
    Region::updateOrCreate([
        'name' => $region,
    ],$region);

    }
}
}
