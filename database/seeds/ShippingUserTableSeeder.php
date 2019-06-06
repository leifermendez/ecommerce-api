<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingUserTableSeeder extends Seeder
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
            DB::table('shipping_addresses')->insert([
                'user_id' => $i,
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
