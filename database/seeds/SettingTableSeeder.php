<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert(array(
            array('meta' => 'limit_item_shopping_cart', 'value' => '50'),
            array('meta' => 'currency', 'value' => 'EUR'),
            array('meta' => 'feed_percentage', 'value' => '0.03'),
            array('meta' => 'feed_amount', 'value' => '3'),
            array('meta' => 'feed_limit_price', 'value' => '100'),
            array('meta' => 'delivery_feed_min', 'value' => '6.50'),
            array('meta' => 'delivery_feed_tax', 'value' => '0.21'),
            array('meta' => 'countries_available', 'value' => 'ES,US'),
            array('meta' => 'stripe_auth_redirect', 'value' => 'http://localhost:4200')
        ));
    }
}
