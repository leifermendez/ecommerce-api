<?php

use Illuminate\Database\Seeder;

class AttributesTableSeeder extends Seeder
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
            DB::table('attributes')->insert([
                'name' => $faker->sentence(2),
                'value' => ($i<5) ? NULL : '{"green":"Color veder","yellow":"color amarillo"}',
                'element_type' =>  ($i<5) ? 'number' : 'select',
                'required' => ($i<6) ? 1: 0
            ]);
        }
    }
}
