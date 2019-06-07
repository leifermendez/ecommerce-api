<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class heroku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heroku';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('¿Quieres continuar en Heroku?')) {
           
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed --class=UsersTableSeeder');
            Artisan::call('db:seed --class=ShopTableSeeder');
            Artisan::call('db:seed --class=ProductsTableSeeder');
            Artisan::call('db:seed --class=PurchaseTableSeeder');
            Artisan::call('db:seed --class=OrderSeeder');
            Artisan::call('db:seed --class=PurchaseDetailTableSeeder');
            Artisan::call('db:seed --class=ZoneAvailableTableSeeder');
            Artisan::call('db:seed --class=CategoriesTableSeeder');
            Artisan::call('db:seed --class=BannersTableSeeder');
            Artisan::call('db:seed --class=AttachedTableSeeder');
            Artisan::call('db:seed --class=AttributesTableSeeder');
            Artisan::call('db:seed --class=CategoryAttributesTableSeeder');
            Artisan::call('db:seed --class=ProductVariableTableSeeder');
            Artisan::call('db:seed --class=UserPaymentSettingTable');
            //Artisan::call('db:seed --class=ShippinShopTableSeeder');
            //Artisan::call('db:seed --class=CommentsTableSeeder');
            Artisan::call('db:seed --class=SettingTableSeeder');
            Artisan::call('db:seed --class=ShippingUserTableSeeder');
            Artisan::call('db:seed --class=AttechedProdcutsTableSeeder');
        }

    }
}
