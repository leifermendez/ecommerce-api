<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WelcomeController extends Controller
{
    protected $permissions;


    public function __construct(PermissionsCheckerInstaller $checker)
    {
        $this->permissions = $checker;
    }

    public function checkSystem()
    {
        $requirements = [
            'php' => [
                'openssl',
                'pdo',
                'mbstring',
                'tokenizer',
                'JSON',
                'cURL',
            ],
            'apache' => [
                'mod_rewrite',
            ],
        ];
        $results = [];

        foreach ($requirements as $type => $requirement) {
            switch ($type) {
                // check php requirements
                case 'php':
                    foreach ($requirements[$type] as $requirement) {
                        $results['requirements'][$type][$requirement] = true;

                        if (!extension_loaded($requirement)) {
                            $results['requirements'][$type][$requirement] = false;

                            $results['errors'] = true;
                        }
                    }
                    break;
                // check apache requirements
                case 'apache':
                    foreach ($requirements[$type] as $requirement) {
                        // if function doesn't exist we can't check apache modules
                        if (function_exists('apache_get_modules')) {
                            $results['requirements'][$type][$requirement] = true;

                            if (!in_array($requirement, apache_get_modules())) {
                                $results['requirements'][$type][$requirement] = false;

                                $results['errors'] = true;
                            }
                        }
                    }
                    break;
            }
        }

        return $results;

    }


    public function welcome()
    {
        try {
            Session::flush();
            return view('installer.welcome', ['system' => $this->checkSystem()]);
        } catch (\Exception $e) {
            Session::flush();
        }
    }

    public function account()
    {
        $permissions = $this->permissions->check(
            config('installer.permissions')
        );

        $form = [
            'APP_NAME' => [
                'name' => 'Nombre de tu app',
                'example' => 'Mi-tienda',
                'type' => 'text',
                'required' => true
            ],
            'APP_URL' => [
                'name' => 'Url de tienda',
                'example' => 'http://mitienda.com',
                'type' => 'url',
                'required' => true
            ],
            'DB_HOST' => [
                'name' => 'DB Host',
                'example' => '127.0.0.1',
                'type' => 'text',
                'required' => true
            ],
            'DB_DATABASE' => [
                'name' => 'DB Nombre',
                'example' => 'my_ecommerce',
                'type' => 'text',
                'required' => true
            ],
            'DB_USERNAME' => [
                'name' => 'DB Usuario',
                'example' => 'my_user_name',
                'type' => 'text',
                'required' => true
            ],
            'DB_PASSWORD' => [
                'name' => 'DB Contraseña',
                'example' => 'my_db_password',
                'type' => 'text',
                'required' => false
            ]
        ];

        return view('installer.account', [
            'permissions' => $permissions,
            'form' => $form
        ]);
    }

    public function saveEnv(Request $request)
    {
        try {

            dd($request->all());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
