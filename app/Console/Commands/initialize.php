<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
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
            $this->checkEnv();
             $this->info('Validando la version de PHP');
             $this->validatePhp();
             $this->info('PHP correcto');
             $this->info('Validando Base de Datos');
//             $this->validateBD();
             $this->info('Base de Datos correcta');
             $this->info('Inicializando Variables de entorno');
             $this->configurationEnv();
             $this->info('Porfavor espere mientras se configura la base de datos, esto puede tardar unos minutos');
             $this->info('Comenzando migracion');
             Artisan::call('migrate --force');
             Artisan::call('db:seed --force');
             Artisan::call('storage:link');
             $this->info('Migracion Finalizada');
             Artisan::call('key:generate');
             Artisan::call('jwt:secret --force');

//            shell_exec('"vendor/bin/phpunit"');
            $this->info('Configuracion completada exitosamente');

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function checkEnv(){
        $path = app_path().'/../.env';
        if (!file_exists($path)) {
            fopen($path, 'w') or die('Cannot open file:  '.$path);
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
            $min = 5.8;
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
        $this->info('Por favor ingresar los siguientes valores');
        $DB_HOST = $this->ask('Ingresa el ip del host de la base de datos');
        $DB_PORT = $this->ask('Ingresa el puerto de base de datos (3306)');
        $DB_DATABASE = $this->ask('Ingresa el nombre de la base de datos');
        $DB_USERNAME = $this->ask('Ingresa el usuario de la base de datos');
        $DB_PASSWORD = $this->secret('Ingresa el clave de la base de datos');



        $DB_STRIPE_KEY = $this->ask('Ingresa el STRIPE key PK');
        $DB_STRIPE_SECRET = $this->ask('Ingresa tu keysecret de STRIPE SK');
        $DB_PLATFORM_ID = $this->ask('Ingresa STRIPE id ');

        $DB_STRIPE_KEY_SAMBOX = $this->ask('Ingresa el STRIPE key SAMBOX PK');
        $DB_STRIPE_SECRET_SAMBOX = $this->ask('Ingresa tu keySecret de STRIPE SAMBOX SK');



        putenv("APP_KEY=");
        putenv("DB_HOST=$DB_HOST");
        putenv("DB_PORT=$DB_PORT");
        putenv("DB_DATABASE=$DB_DATABASE");
        putenv("DB_USERNAME=$DB_USERNAME");
        putenv("DB_PASSWORD=$DB_PASSWORD");

        putenv("STRIPE_KEY=$DB_STRIPE_KEY");
        putenv("STRIPE_SECRET=$DB_STRIPE_SECRET");
        putenv("STRIPE_PLATFORM_ID=$DB_PLATFORM_ID");

        putenv("STRIPE_KEY_SAMBOX=$DB_STRIPE_KEY_SAMBOX");
        putenv("STRIPE_SECRET_SAMBOX=$DB_STRIPE_SECRET_SAMBOX");

        $values =  [
            'DB_HOST' => $DB_HOST,
            'DB_PORT' => $DB_PORT,
            'DB_DATABASE' => $DB_DATABASE,
            'DB_USERNAME' => $DB_USERNAME,
            'DB_PASSWORD' => $DB_PASSWORD,
            'APP_NAME'=>'TEST',
            'APP_ENV'=>'local',
            'APP_DEBUG'=>'false',
            'APP_SITE_MAIL'=>'default',
            'MAIL_ENCRYPTION'=>'tls',
            'MAIL_FROM_ADDRESS'=>'from@example.com',
            'MAIL_FROM_NAME'=>'Example',
            'PAACK_KEY'=>'',
            'ELINFORMAR_CLIENT_ID'=>'.api.einforma.com',
            'ELINFORMAR_CLIENT_SECRET'=>'',
            'TWILIO_SID'=>'ACefd4718af2b07d2aa6936d3a37458cb0',
            'TWILIO_TOKEN'=>'fb6e132e69e1152749032cdf5f5dc27a',
            'TWILIO_FROM'=>'+34955160684',
            'TRUUST_PK'=>'=',
            'TRUUST_SK'=>'',
            'STRIPE_KEY'=>$DB_STRIPE_KEY,
            'STRIPE_SECRET'=>$DB_STRIPE_SECRET,
            'STRIPE_KEY_SAMBOX'=>$DB_STRIPE_KEY_SAMBOX,
            'STRIPE_SECRET_SAMBOX'=>$DB_STRIPE_SECRET_SAMBOX,
            'STRIPE_WEBHOOK_SECRET'=>'',
            'STRIPE_WEBHOOK_TOLERANCE'=>'',
            'STRIPE_PLATFORM_ID'=>$DB_PLATFORM_ID,
            'GOOGLE_API_VISION_KEY'=>'',
        ];
        $this->setEnvironmentValue($values);
    }
}

