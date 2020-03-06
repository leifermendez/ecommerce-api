<?php

use Illuminate\Database\Seeder;
use App\variation_product;

class ProductVariableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('variation_products')->truncate();
        factory(variation_product::class, 10)->create();
    }
}
