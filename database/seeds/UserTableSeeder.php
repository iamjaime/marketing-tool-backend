<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'name' => 'admin',
            'email' => Config::get('marketingtool.admin_email'),
            'password' => bcrypt(Config::get('marketingtool.admin_password')),
            'primary_language_id' => 1 //default english
        ];

        $usr = new User();
        $usr->create($user);
    }
}
