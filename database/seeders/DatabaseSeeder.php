<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ServiceTypeSeeder::class,
            ServiceWindowSeeder::class,
            ClientSeeder::class,
            SystemSettingSeeder::class,
            DemoQueueSeeder::class,
        ]);
    }
}
