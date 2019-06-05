<?php

use Illuminate\Database\Seeder;

class ProductVariableTableSeeder extends Seeder
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
            DB::table('variation_products')->insert([
                'price_normal' => $faker->randomFloat(2, 100, 120),
                'price_regular' => $faker->randomFloat(2, 121, 400),
                'product_id' => ($i < 3) ? 2 : $i + 1,
                'quantity' => $faker->numberBetween(1, 5),
                'label' => $faker->sentence(2),
                'attached_id' => $i + 1,
                'observation' => $faker->sentence(5),
                'weight' => $faker->numberBetween(20, 60),
                'width' => $faker->numberBetween(15, 60),
                'height' => $faker->numberBetween(20, 60),
                'length' => $faker->numberBetween(20, 60),
                'delivery' => ($i < 6) ? 1 : 0
            ]);
        }
    }
}
