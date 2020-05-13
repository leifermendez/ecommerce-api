<?php


namespace App\Http\Controllers\Installer;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnvironmentInstaller
{

    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (!file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param Request $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = 'Success';

        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return $message;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function saveFileWizard(Request $request)
    {
        $results = 'Env success';

        $envFileData =
            'APP_NAME=\'' . $request->APP_NAME . "'\n" .
            'APP_ENV=' . 'local' . "\n" .
            'APP_KEY=' . 'base64:' . base64_encode(Str::random(32)) . "\n" .
            'APP_SITE_MAIL=' . "default\n" .
            'APP_DEBUG=' . 'true' . "\n" .
            'APP_LOG_LEVEL=' . 'debug' . "\n" .
            'APP_URL=' . $request->APP_URL . "\n\n" .
            'DB_CONNECTION=' . 'mysql' . "\n" .
            'DB_HOST=' . $request->DB_HOST . "\n" .
            'DB_PORT=' . '3306' . "\n" .
            'DB_DATABASE=' . $request->DB_DATABASE . "\n" .
            'DB_USERNAME=' . $request->DB_USERNAME . "\n" .
            'DB_PASSWORD=' . $request->DB_PASSWORD . "\n\n" .
            'TWILIO_SID=' . '' . "\n" .
            'TWILIO_TOKEN=' . '' . "\n" .
            'TWILIO_FROM=' . '' . "\n" .
            'BROADCAST_DRIVER=' . 'log' . "\n" .
            'CACHE_DRIVER=' . 'file' . "\n" .
            'SESSION_DRIVER=' . 'file' . "\n" .
            'QUEUE_DRIVER=' . 'sync' . "\n\n" .
            'REDIS_HOST=' . '127.0.0.1' . "\n" .
            'REDIS_PASSWORD=' . 'null' . "\n" .
            'REDIS_PORT=' . '6379' . "\n\n" .
            'MAIL_DRIVER=' . 'smtp' . "\n" .
            'MAIL_HOST=' . 'smtp.mailtrap.io' . "\n" .
            'MAIL_PORT=' . '2525' . "\n" .
            'MAIL_USERNAME=' . 'null' . "\n" .
            'MAIL_PASSWORD=' . 'null' . "\n" .
            'MAIL_ENCRYPTION=' . 'null' . "\n\n" .
            'STRIPE_PLATFORM_ID=\'' . 'null' . "'\n" .
            'STRIPE_KEY=' . 'null' . "\n" .
            'STRIPE_SECRET=' . 'null' . "\n\n" .
            'PUSHER_APP_ID=' . '' . "\n" .
            'PUSHER_APP_KEY=' . '' . "\n" .
            'PUSHER_APP_SECRET=' . '' . "\n" .
            'FACEBOOK_ID=' . '' . "\n" .
            'GOOGLE_ID=' . '';

        try {
            file_put_contents($this->envPath, $envFileData);
            return redirect()->route('InstallerMigrations');
        } catch (Exception $e) {
            $results = $e->getMessage();
            dd($results);
        }

        return $results;
    }
}
