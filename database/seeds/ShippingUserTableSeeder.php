<?php

use App\shipping_address;
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
        DB::table( 'shipping_addresses' )->truncate();
        factory(shipping_address::class, 1)->create();
    }
}
