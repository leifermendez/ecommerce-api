<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPaymentSettingTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 1; $i < 3; $i++) {
            DB::table('user_payments')->insert([
                'user_id' => $i,
                'payment_option' => 'stripe',
                'payment_email' => 'pagos@compnay.com',
                'iban' => $faker->ean8,
                'observation' => 'Alguna observacion',
                'account_name' => $faker->name,
                'account_lastname' => $faker->lastName,
                'primary' => 1
            ]);
        }
    }
}
