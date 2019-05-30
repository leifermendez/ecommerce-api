<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseTableSeeder extends Seeder
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
            DB::table('purchase_orders')->insert([
                'uuid' => ($i<3) ? 'f0118c35-af32-3df9-a429-ba07d8dc462f' : $faker->uuid,
                'uuid_shipping' => $faker->md5,
                'amount' => $faker->randomFloat(2,1,9999),
                'feed' => $faker->randomFloat(2,2,20),
                'amount_shipping' => $faker->randomFloat(2,2,20),
                'user_id' => ($i<3) ? ($i+1) : 1,
                'shop_id' => ($i<3) ? ($i+1) : 1,
                'shipping_address_id' => ($i<3) ? ($i+1) : 1,
                'status' => ($i<5) ? 'success' : 'cancel'
            ]);
        }
    }
}
