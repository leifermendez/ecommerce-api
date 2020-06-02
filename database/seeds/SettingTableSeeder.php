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
            array('meta' => 'countries_available', 'value' => 'ES,'),
            array('meta' => 'stripe_auth_redirect', 'value' => 'http://localhost:4200'),
            array('meta' => 'search_range_km', 'value' => '10'),
            array('meta' => 'discount_to_supplier', 'value' => '1'),
            array('meta' => 'auto_delivery', 'value' => '0'),
            array('meta' => 'auto_sms', 'value' => '0'),
            array('meta' => 'range_closed', 'value' => '0'),
            array('meta' => 'google_vision', 'value' => '0'),
            array('meta' => 'only_user_confirmed', 'value' => '0'),
            array('meta' => 'marketplace', 'value' => '1'),
            array('meta' => 'schedule_active', 'value' => '0'),
            array('meta' => 'edge_time', 'value' => '30'),
            array('meta' => 'mode_catalogue', 'value' => '0'),
        ));
    }
}
