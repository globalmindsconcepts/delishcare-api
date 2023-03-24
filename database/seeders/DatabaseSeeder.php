<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        (new AdminTableSeeder)->run();
        (new PackageTableSeeder)->run();
        (new RankTableSeeder)->run();
        (new SettingsTableSeeder)->run();
        (new IncentiveTableSeeder)->run();
        (new ServiceProviderTableSeeder)->run();
        (new ProductServiceTableSeeder)->run();
        (new ProductTableSeeder)->run();
        (new SingleUserSeeder)->run();
    }
}
