<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //$this->call(UserTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(PermissionNameTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(CounterTableSeeder::class);
        $this->call(SaleCorrectionSeeder::class);
    }
}
