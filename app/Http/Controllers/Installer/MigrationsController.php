<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class MigrationsController extends Controller
{
    private $databaseManager;


    public function __construct(DatabaseManagerInstaller $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    public function database(Request $request)
    {
        $response = $this->databaseManager->migrateAndSeed();
        User::where('email', 'admin@mail.com')
            ->update([
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('re_password'))
            ]);
        $response = [
            'user' => [
                'email' => $request->input('email'),
                'password' => $request->input('re_password')
            ],
            'migrations' => $response
        ];

        return view('installer.overview', ['message' => $response]);

    }

    public function overview()
    {
        $form = [
            'email' => [
                'name' => 'Email',
                'example' => '',
                'type' => 'email'
            ],
            'password' => [
                'name' => 'Contraseña',
                'example' => '',
                'type' => 'password'
            ],
            're.password' => [
                'name' => 'Repetir contraseña',
                'example' => '',
                'type' => 'password'
            ]
        ];
        return view('installer.migrations', ['form' => $form]);
    }
}
