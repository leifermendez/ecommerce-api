<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
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
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }

            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) return false;
        return true;

    }

    public function mailSetting()
    {

    }

    public function index()
    {
        $options = [
            'mail' => [
                'MAIL_DRIVER' => [
                    'value' => env('MAIL_DRIVER', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'MAIL_DRIVER'
                ],
                'MAIL_HOST' => [
                    'value' => env('MAIL_HOST', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'MAIL_HOST'
                ],
                'MAIL_PORT' => [
                    'value' => env('MAIL_PORT', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'MAIL_PORT'
                ],
                'MAIL_USERNAME' => [
                    'value' => env('MAIL_USERNAME', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'MAIL_USERNAME'
                ],
                'MAIL_PASSWORD' => [
                    'value' => env('MAIL_PASSWORD', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'MAIL_PASSWORD'
                ],
                'MAIL_ENCRYPTION' => [
                    'value' => env('MAIL_ENCRYPTION', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'MAIL_ENCRYPTION'
                ]
            ],
            'sms' => [
                'TWILIO_SID' => [
                    'value' => env('TWILIO_SID', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'TWILIO_SID'
                ],
                'TWILIO_TOKEN' => [
                    'value' => env('TWILIO_TOKEN', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'TWILIO_TOKEN'
                ],
                'TWILIO_FROM' => [
                    'value' => env('TWILIO_FROM', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'TWILIO_FROM'
                ],
            ],
            'stripe' => [
                'STRIPE_PLATFORM_ID' => [
                    'value' => env('STRIPE_PLATFORM_ID', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'STRIPE_PLATFORM_ID'
                ],
                'STRIPE_KEY' => [
                    'value' => env('STRIPE_KEY', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'STRIPE_KEY'
                ],
                'STRIPE_SECRET' => [
                    'value' => env('STRIPE_SECRET', ''),
                    'example' => '',
                    'type' => 'text',
                    'name' => 'STRIPE_SECRET'
                ],
            ],
            'templates' => [
                [
                    'name' => 'Template 1',
                    'url' => 'https://media-mochileros.s3.us-east-2.amazonaws.com/template-1.zip',
                    'image' => 'https://i.imgur.com/bvSdPSK.png'
                ],
//                [
//                    'name' => 'Template 2',
//                    'url' => 'http://lol.com/zip2.zip',
//                    'image' => 'https://i.imgur.com/BjgmZLQ.png'
//                ],
//                [
//                    'name' => 'Template 3',
//                    'url' => 'http://lol.com/zip3.zip',
//                    'image' => 'https://i.imgur.com/BjgmZLQ.png'
//                ]
            ],
            'general' => [
                'ENV_ENDPOINT' => [
                    'name' => 'API',
                    'value' => env('APP_URL', '').'/api/1.0',
                    'type' => 'url',
                    'readonly' => false,
                    'example' => ''
                ],
                'ENV_STRIPE_PK' => [
                    'name' => 'Stripe (pk_)',
                    'value' => env('STRIPE_KEY', ''),
                    'type' => 'text',
                    'readonly' => true,
                    'example' => ''
                ],
                'ENV_GOOGLE_ID' => [
                    'name' => 'Google ID',
                    'value' => env('GOOGLE_ID', ''),
                    'type' => 'text',
                    'readonly' => false,
                    'example' => ''
                ],
                'ENV_FACEBOOK_ID' => [
                    'name' => 'Facebook ID',
                    'value' => env('FACEBOOK_ID', ''),
                    'type' => 'text',
                    'readonly' => false,
                    'example' => ''
                ]
            ]
        ];
        return view('home', ['options' => $options]);
    }

    public function saveMail(Request $request)
    {
        try {
            $values = [
                'MAIL_DRIVER' => $request->input('MAIL_DRIVER'),
                'MAIL_HOST' => $request->input('MAIL_HOST'),
                'MAIL_PORT' => $request->input('MAIL_PORT'),
                'MAIL_USERNAME' => $request->input('MAIL_USERNAME'),
                'MAIL_PASSWORD' => $request->input('MAIL_PASSWORD'),
                'MAIL_ENCRYPTION' => $request->input('MAIL_ENCRYPTION')
            ];
            $this->setEnvironmentValue($values);

            return redirect()->route('AdminHome');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function saveSMS(Request $request)
    {
        try {
            $values = [
                'TWILIO_SID' => $request->input('TWILIO_SID'),
                'TWILIO_TOKEN' => $request->input('TWILIO_TOKEN'),
                'TWILIO_FROM' => $request->input('TWILIO_FROM')
            ];
            $this->setEnvironmentValue($values);

            return redirect()->route('AdminHome');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function saveStripe(Request $request)
    {
        try {
            $values = [
                'STRIPE_PLATFORM_ID' => $request->input('STRIPE_PLATFORM_ID'),
                'STRIPE_SECRET' => $request->input('STRIPE_SECRET'),
                'STRIPE_KEY' => $request->input('STRIPE_KEY')
            ];
            $this->setEnvironmentValue($values);

            return redirect()->route('AdminHome');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function saveTemplate(Request $request)
    {
        try {


            $values = [
                'ENV_ENDPOINT' => $request->input('ENV_ENDPOINT'),
                'ENV_STRIPE_PK' => $request->input('ENV_STRIPE_PK'),
                'ENV_GOOGLE_ID' => $request->input('ENV_GOOGLE_ID'),
                'ENV_FACEBOOK_ID' => $request->input('ENV_FACEBOOK_ID')
            ];
            $this->setEnvironmentValue($values);
            $env = [
                'ENV_ENDPOINT' => $request->input('ENV_ENDPOINT'),
                'ENV_STRIPE_PK' => $request->input('ENV_STRIPE_PK'),
                'ENV_GOOGLE_ID' => $request->input('ENV_GOOGLE_ID'),
                'ENV_FACEBOOK_ID' => $request->input('ENV_FACEBOOK_ID')
            ];
            $template = $request->input('template');
            $pathUnzip = $this->downloadTemplate($template);
            $files = scandir(public_path(), 1);
            foreach ($files as $file) {
                if (strpos($file, '.js') !== false) {
                    $this->findAndReplace($pathUnzip . '/' . $file, $env);
                }
            }
//            $this->makeZip();
//            $values = [
//                'STRIPE_PLATFORM_ID' => $request->input('STRIPE_PLATFORM_ID'),
//                'STRIPE_SECRET' => $request->input('STRIPE_SECRET'),
//                'STRIPE_KEY' => $request->input('STRIPE_KEY')
//            ];
//            $this->setEnvironmentValue($values);
//
            return redirect()->route('AdminHome');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Descargar template ZIP desde fuente
     */
    private function downloadTemplate($filename = '')
    {
        try {
            $zipper = new \Madnest\Madzipper\Madzipper;
            $url = $filename;
            $filename = basename($url);
            if (file_put_contents($filename, file_get_contents($url))) {
                $pathZip = public_path() . '/' . $filename;
                $zipper->make($pathZip)->folder('dist')->extractTo(public_path());
                return public_path();
            } else {
                return null;
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Creamos ZIP del template ya con las variables
     */
    private function makeZip()
    {
        try {
            $pathRawTemplate = public_path() . '/template-raw';
            $zipper = new \Madnest\Madzipper\Madzipper;
            $files = glob($pathRawTemplate);
            $zipper->make('awesome-website.zip')->add($files)->close();
            $this->deleteDir($pathRawTemplate);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Buscar y remplazar las variables
     */
    private function findAndReplace($file, $env = [])
    {
        try {
            $str = file_get_contents($file);
            foreach ($env as $key => $value) {
                $str = str_replace($key, $value, $str);
            }
            file_put_contents($file, $str);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}
