<?php

use App\products;
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
        DB::table('products')->truncate();
        
        DB::table('products')->insert([
            'name' => 'Producto Prueba',
            'short_description' => $faker->sentence(8),
            'featured' => 'premium',
            'product_type' =>'digital',
            'description' => $faker->sentence(20),
            'shop_id' => 1,
            'status' => 'available',
        ]);
        factory(products::class, 4)->create();
    }
}
