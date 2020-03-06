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

    protected $signature = 'initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Puesta en Marcha';

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
        try {
            $this->info('Validando la version de PHP');
            $this->validatePhp();
            $this->info('PHP correcto');
            $this->info('Validando Base de Datos');
            $this->validateBD();
            $this->info('Base de Datos correcta');
            $this->info('Inicializando Variables de entorno');
            $this->configurationEnv();
            $this->info('Precione Entrer para finalizar');
            Artisan::call('key:generate');
            Artisan::call('jwt:secret');
            $this->info('Porfavor espere mientras se configura la base de datos');
            Artisan::call('migrate:refresh');
            Artisan::call('db:seed');
            $this->info('Configuracion completada exitosamente');
            
        } catch (Exception $e) {
            $this->error($e);
        }
    }

    public function setEnvironmentValue(array $values){
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
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }
            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) return false;
        return true;
    }

    public function validatePhp(){
        $phpv = phpversion();
        if ($phpv < 7.2) {
            $this->error('La version PHP debe ser mayor o igual a 7.2');
            // return false;
        }
        //Validando php crul
        if  (!in_array ('curl', get_loaded_extensions())) {
            $this->error('Se necesita instalar CRUL en PHP para un funcionamiento correcto');
        }
    }

    public function validateBD(){
        $db = shell_exec('mysql --version');
        $data = explode(",", $db);
        if (strpos($data[0], 'Distrib') === false) {
            $this->error('No tienes base de datos instalada');
            // return false;
        }
        $position = strpos($data[0], 'Distrib');
        $mysql = substr($data[0], ($position +8),strlen($data[0]));
        $version = explode("-", $mysql);
        if (isset($version[1])) {
            $min = 10.2;
            $mensaje = 'La version minima de MariaDB debe ser 10.2';
        }else{
            $min = 5.6;
            $mensaje = 'La version minima de MySql debe ser 5.8';
        }
        $ver = explode(".", $version[0]);
        if (($ver[0] .'.'. $ver[1]) < $min) {
            $this->error($mensaje);
            // return false;
        }
    }

    public function configurationEnv(){
        $name = getenv('APP_NAME');
        if ($name == null) {
            shell_exec('cp .env.example .env');
        }
        $this->info('Por favor ingresar los siguientes valores');
        $DB_HOST = $this->ask('Ingresa el ip del host de la base de datos');
        $DB_PORT = $this->ask('Ingresa el puerto de base de datos');
        $DB_DATABASE = $this->ask('Ingresa el nombre de la base de datos');
        $DB_USERNAME = $this->ask('Ingresa el usuario de la base de datos');
        $DB_PASSWORD = $this->secret('Ingresa el calve de la base de datos');
        $values =  [
            'DB_HOST' => $DB_HOST,
            'DB_PORT' => $DB_PORT,
            'DB_DATABASE' => $DB_DATABASE,
            'DB_USERNAME' => $DB_USERNAME,
            'DB_PASSWORD' => $DB_PASSWORD,
            'APP_DEBUG'=>'false',
            'APP_SITE_MAIL'=>'alterhome',
            'MAIL_ENCRYPTION'=>'tls',
            'MAIL_FROM_ADDRESS'=>'from@example.com',
            'MAIL_FROM_NAME'=>'Example',
            'PAACK_KEY'=>'3c8244a87b97e6ba9904617951dc10dd340d0b6c',
            'ELINFORMAR_CLIENT_ID'=>'2ctnq8nqiobu91kpwl0hzwfl3b28x6iwf1f08fyp.api.einforma.com',
            'ELINFORMAR_CLIENT_SECRET'=>'wl9e1mPswyEMA5txFTY6NlqDndLOzbCleUJux57FE1o',
            'TWILIO_SID'=>'ACefd4718af2b07d2aa6936d3a37458cb0',
            'TWILIO_TOKEN'=>'fb6e132e69e1152749032cdf5f5dc27a',
            'TWILIO_FROM'=>'+34955160684',
            'TRUUST_PK'=>'pk_production_YXBhdHhlZS1vbi10aW1lLXMtbC0=',
            'TRUUST_SK'=>'sk_production_VC7sKufCPudxH0Mh6eU51CHX',
            'STRIPE_KEY'=>'pk_test_iOoJca2tObgjRwE7xbi0T3MM008BdX4xYU',
            'STRIPE_SECRET'=>'sk_test_G9Qs20pym4a7Nr43SW3F3JDs00lEIQasj9',
            'STRIPE_WEBHOOK_SECRET'=>'',
            'STRIPE_WEBHOOK_TOLERANCE'=>'',
            'STRIPE_PLATFORM_ID'=>'',
            'GOOGLE_API_VISION_KEY'=>'AIzaSyDAFzd-3P2Wri4m6jgZflzNNUwC5yxjrJ0',
        ];
        $this->setEnvironmentValue($values);
    }
}

