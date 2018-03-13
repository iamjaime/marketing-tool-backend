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
        $this->call(LanguageTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(ServiceProviderSeeder::class);
        $this->call(ServiceTableSeeder::class);
        $this->call('CountriesSeeder');
        $this->command->info('Seeded the countries!');
    }
}
