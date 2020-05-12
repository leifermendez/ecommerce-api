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
                    'url' => '',
                    'image' => 'https://i.imgur.com/BjgmZLQ.png'
                ],
                [
                    'name' => 'Template 2',
                    'url' => '',
                    'image' => 'https://i.imgur.com/BjgmZLQ.png'
                ],
                [
                    'name' => 'Template 3',
                    'url' => '',
                    'image' => 'https://i.imgur.com/BjgmZLQ.png'
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
}
