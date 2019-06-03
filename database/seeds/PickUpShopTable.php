<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PickUpShopTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 1; $i < 5; $i++) {
            DB::table('shipping_pickup_addresses')->insert([
                'shop_id' => $i,
                'country' => ($i<3) ? 'ES' :  $faker->countryCode,
                'state' => $faker->state,
                'district' => $faker->city,
                'address' => $faker->streetAddress,
                'zip_code' => '28039',
                'instructions' => 'Tocar el timbre 3 veces'
            ]);
        }
    }
}
