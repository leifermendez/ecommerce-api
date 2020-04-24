<?php

use Illuminate\Database\Seeder;
use App\attributes;

class AttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attributes')->truncate();
        factory(attributes::class, 10)->create();
    }
}
