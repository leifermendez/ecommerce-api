<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
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
            DB::table('orders')->insert([
                'amount' => $faker->randomFloat(2,1,9999),
                'description' => $faker->sentence(8),
                'user_id' => ($i<3) ? ($i+1) : 1,
                'observation' => $faker->sentence(10),
                'purchase_id' => ($i<7) ? ($i+1) : 1,
                'platform_id' => $faker->numberBetween(1,3),
                'status' => ($i<3) ? 'wait' : 'success',
            ]);
        }
    }
}
