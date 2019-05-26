<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BannersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $now = Carbon::now();
        for ($i = 0; $i < 10; $i++) {
            DB::table('banners')->insert([
                'attached_id' => $i+1,
                'shop_id' => $i+1,
                'title' => $faker->sentence(2),
                'description' => $faker->sentence(8),
                'media_type' => ($i<6) ? 'image' : 'video',
                'start' => $now->toDateTimeString(),
                'finish' => $now->addMonth()->toDateTimeString(),
                'url' => $faker->url
            ]);
        }
    }
}
