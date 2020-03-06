<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\shipping_pickup_address;

class PickUpShopTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipping_pickup_addresses')->truncate();
        factory(shipping_pickup_address::class, 1)->create();
    }
}