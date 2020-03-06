<?php

use App\shop;
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
        DB::table('shops')->truncate();
        
        DB::table('shops')->insert([
            'users_id' => 2,
            'name'=> $faker->company,
            'address' => $faker->address,
            'slug' => $faker->slug,
            'zip_code' => '28039',
            'legal_id' => $faker->ean8,
            'email_corporate' => $faker->companyEmail,
            'image_cover' => 1,
            'image_header' => 1,
            'phone_mobil' => $faker->phoneNumber,
            'phone_fixed' => $faker->phoneNumber,
            'meta_key' => $faker->sentence($nbWords = 6, $variableNbWords = true),
            'terms_conditions' => $faker->text($maxNbChars = 190),
            'polity_privacy' => $faker->text($maxNbChars = 190)
        ]);
        
        // factory(shop::class, 5)->create();
    }
}
