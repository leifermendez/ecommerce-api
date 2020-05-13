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
        $this->create();
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

    public function create()
    {
        $installedLogFile = storage_path('installed');

        $dateStamp = date('Y/m/d h:i:sa');

        if (! file_exists($installedLogFile)) {
            $message = 'install_create_'.$dateStamp."\n";

            file_put_contents($installedLogFile, $message);
        } else {
            $message = 'install_update_'.$dateStamp;

            file_put_contents($installedLogFile, $message.PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        return $message;
    }
}
