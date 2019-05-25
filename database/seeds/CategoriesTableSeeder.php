<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
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
            DB::table('categories')->insert([
                'name' => $faker->sentence(2),
                'description' => $faker->sentence(8),
                'image' => ($i+1),
                'icon' => 'fa fa-beer',
                'child' => ($i<5) ? ($i+1) : 1,
                'order' => ($i+1)
            ]);
        }
    }
}
