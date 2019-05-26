<?php

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 1; $i < 5; $i++) {
            DB::table('shops')->insert([
                'users_id' => $i,
                'name'=> $faker->company,
                'address' => $faker->address,
                'slug' => $faker->slug,
                'zip_code' => ($i==1) ? '28039' : $faker->postcode,
                'legal_id' => $faker->ean8,
                'email_corporate' => $faker->companyEmail,
                'image_cover' => $i,
                'image_header' => $i,
                'phone_mobil' => $faker->phoneNumber,
                'phone_fixed' => $faker->phoneNumber,
                'meta_key' => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'terms_conditions' => $faker->text($maxNbChars = 190),
                'polity_privacy' => $faker->text($maxNbChars = 190)
            ]);
        }
    }
}
