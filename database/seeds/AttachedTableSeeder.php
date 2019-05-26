<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttachedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 30; $i++) {
            DB::table('attacheds')->insert([
                'name' => Str::random(20),
                'users_id' => $i+1,
                'small' => $faker->imageUrl(250,120,'cats'),
                'medium' => $faker->imageUrl(480,240,'cats'),
                'large' => $faker->imageUrl(800,480,'cats'),
                'media_type' => 'image'
            ]);
        }
    }
}
