<?php

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'service_provider_id' => 1,
                'name' => 'likes'
            ],
            [
                'service_provider_id' => 1,
                'name' => 'posts'
            ],
            [
                'service_provider_id' => 1,
                'name' => 'shares'
            ],
            [
                'service_provider_id' => 1,
                'name' => 'comments'
            ]
        ];

        foreach($services as $service)
        {
            $s = new Service();
            $s->create($service);
        }
    }
}
