<?php

use Illuminate\Database\Seeder;

class CategoryAttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('category_attributes')->insert([
                'category_id' => ($i<6) ? 1: $i+1,
                'attributes_id' => ($i<6) ? 1: $i+1
            ]);
        }
    }
}
