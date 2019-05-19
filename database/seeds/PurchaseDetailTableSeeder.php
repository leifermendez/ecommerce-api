<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseDetailTableSeeder extends Seeder
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
            DB::table('purchase_details')->insert([
                'purchase_id' => ($i<3) ? ($i+1) : 1,
                'product_id' => ($i<3) ? ($i+1) : 1,
                'product_qty' => $faker->numberBetween(1,5),
                'product_label' => $faker->company,
                'product_amount' => $faker->randomFloat(2,2,20),
                'shop_id' => ($i<3) ? ($i+1) : 1
            ]);
        }
    }
}
