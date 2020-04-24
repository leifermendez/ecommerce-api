<?php

use App\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        DB::table( 'users' )->truncate();
        $users =[
            [
                'name' => 'Admin Test',
                'email' => 'admin@mail.com',
                'password' => bcrypt('123456'),
                'phone' => $faker->phoneNumber,
                'confirmed' => 1,
                'avatar' => 'http://lorempixel.com/640/480/',
                'header' => 'http://lorempixel.com/1200/680/',
                'role' => 'admin',
                'referer_code' => $faker->swiftBicNumber,
            ],
            [
                'name' => 'Tienda Test',
                'email' => 'shop@mail.com',
                'password' => bcrypt('123456'),
                'phone' => $faker->phoneNumber,
                'confirmed' => 1,
                'avatar' => 'http://lorempixel.com/640/480/',
                'header' => 'http://lorempixel.com/1200/680/',
                'role' => 'shop',
                'referer_code' => $faker->swiftBicNumber,
            ],
            [
                'name' => 'Cliente Test',
                'email' => 'cliente@mail.com',
                'password' => bcrypt('123456'),
                'phone' => $faker->phoneNumber,
                'confirmed' => 1,
                'avatar' => 'http://lorempixel.com/640/480/',
                'header' => 'http://lorempixel.com/1200/680/',
                'role' => 'user',
                'referer_code' => $faker->swiftBicNumber,
            ],
        ];
        
        DB::table('users')->insert($users);
    }
}
