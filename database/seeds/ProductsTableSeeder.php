<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
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
            DB::table('products')->insert([
                'name' => $faker->sentence(2),
                'short_description' => $faker->sentence(8),
                'featured' => ($i<3) ? 'premium' : 'regular',
                'product_type' => ($i<5) ? 'digital':'physical',
                'description' => $faker->sentence(20),
                'shop_id' => ($i<5) ? ($i+1) : 1,
                'category_id' => ($i<3) ? ($i+1) : 1,
                'status' => ($i<6) ? 'available':'unavailable',
            ]);
        }
    }
}
