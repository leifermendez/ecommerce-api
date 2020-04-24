<?php

use App\user_payment;
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
        DB::table('user_payments')->truncate();
        factory(user_payment::class, 3)->create();
    }
}
