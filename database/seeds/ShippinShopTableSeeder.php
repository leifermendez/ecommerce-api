<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ShippinShopTableSeeder extends Seeder
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
            DB::table('shipping_pickup_addresses')->insert([
                'shop_id' => $i+1,
                'country' => ($i<3) ? 'ES' :  $faker->countryCode,
                'state' => $faker->state,
                'district' => $faker->city,
                'address' => $faker->streetAddress,
                'zip_code' => '28039',
                'instructions' => 'preguntar por el encargado de bodega'
            ]);
        }
    }
}
