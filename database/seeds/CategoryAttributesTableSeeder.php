<?php

use Illuminate\Database\Seeder;
use App\category_attributes;
class CategoryAttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
        DB::table('category_attributes')->truncate();
        factory(category_attributes::class, 10)->create();
    }
}
