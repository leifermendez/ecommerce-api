<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('comments')->insert([
                'user_id' => $i+1,
                'product_id' => ($i<3) ? 2 : $i+1,
                'shop_id' => ($i<4) ? 2 : $i+1,
                'purchase_id' => $i+1,
                'attached_id' => $i+1,
                'score' => 3,
                'comment' => $faker->sentence(8)
            ]);
        }
    }
}
