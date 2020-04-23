<?php

use Illuminate\Database\Seeder;

class PlatformPymentsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->truncate();

        DB::table('platform_payments')->insert([
            'name' => 'stripe',
            'label' => 'Stripe',
            'description' => 'Stripe',
            'image' =>'1'
        ]);
    }
}
