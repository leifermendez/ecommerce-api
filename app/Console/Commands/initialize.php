<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    public function setEnvironmentValue(array $values)
    {

        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {

                $str .= "\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}={$envValue}\n";
                } else {
                    //$str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }

            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) return false;
        return true;

    }

    protected $signature = 'initialize {force?}';

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
        
        if ($this->confirm('Quieres continuar?')) {
            $force = $this->argument('force');
            
            $values = [
                'PAACK_KEY' => '',
                'ELINFORMAR_CLIENT_ID' => '',
                'ELINFORMAR_CLIENT_SECRET' => '',
                'TWILIO_SID' => '',
                'TWILIO_TOKEN' => '',
                'TWILIO_FROM' => '',
                'TRUUST_PK' => '',
                'TRUUST_SK' => '',
                'STRIPE_KEY' => '',
                'STRIPE_SECRET' => '',
                'STRIPE_WEBHOOK_SECRET' => '',
                'STRIPE_WEBHOOK_TOLERANCE' => '',
                'STRIPE_PLATFORM_ID' => ''
            ];
            $this->setEnvironmentValue($values);
            
            if($force=='force'){
                Artisan::call('key:generate');
                Artisan::call('jwt:secret');  
            }

            Artisan::call('migrate:refresh');
            Artisan::call('db:seed --class=UsersTableSeeder');
            Artisan::call('db:seed --class=ShopTableSeeder');
            Artisan::call('db:seed --class=ProductsTableSeeder');
            Artisan::call('db:seed --class=PurchaseTableSeeder');
            Artisan::call('db:seed --class=OrderSeeder');
            Artisan::call('db:seed --class=PurchaseDetailTableSeeder');
            Artisan::call('db:seed --class=ZoneAvailableTableSeeder');
            Artisan::call('db:seed --class=CategoriesTableSeeder');
        }

    }
}
