<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use App\attached_products;

class AttechedProdcutsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attached_products')->truncate();
        factory(attached_products::class, 3)->create();
    }
}
