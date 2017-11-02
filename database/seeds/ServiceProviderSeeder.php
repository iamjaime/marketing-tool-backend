<?php

use Illuminate\Database\Seeder;
use App\Models\ServiceProvider;

class ServiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Initially seed the database with 1 service provider.
        $data = ['name' => 'Facebook'];

       $serviceProvider = new ServiceProvider();
       $serviceProvider->create($data);
    }
}
