<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OverViewController extends Controller
{
//    protected $permissions;
//
//
//    public function __construct(PermissionsCheckerInstaller $checker)
//    {
//        $this->permissions = $checker;
//    }

    public function overview()
    {
        return view('installer.overview');
    }


}
