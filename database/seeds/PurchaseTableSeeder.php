<?php

use App\purchase_order;
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
        DB::table('purchase_orders')->truncate();

        DB::table('purchase_orders')->insert([
            'uuid' => 'f0118c35-af32-3df9-a429-ba07d8dc462f',
            'uuid_shipping' => $faker->md5,
            'amount' => $faker->randomFloat(2,1,9999),
            'feed' => $faker->randomFloat(2,2,20),
            'amount_shipping' => $faker->randomFloat(2,2,20),
            'user_id' => 2,
            'shop_id' => 1,
            'shipping_address_id' => 1,
            'status' =>'success',
        ]);

        // factory(purchase_order::class, 10)->create();
    }
}
