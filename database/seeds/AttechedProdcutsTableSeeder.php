<?php

use Faker\Factory;
use Illuminate\Database\Seeder;

class AttechedProdcutsTableSeeder extends Seeder
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
            DB::table('attached_products')->insert([
                'attached_id' => $i+1,
                'product_id' => ($i < 3) ? 1 : $i + 1
            ]);
        }
    }
}
