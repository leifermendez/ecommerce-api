<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Notifications\_MailMarketing;

class MailMarketing extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $users = User::where('status','available')->get();

        $user = [
            'tester@alterhome.es',
            's.perini@alterhome.es',
            's.moutawakil@alterhome.es',
            's.aliseda@alterhome.es',
            'rrhh@alterhome.es',
            'patricia@alterhome.es',
            'montse@alterhome.es',
            'luciano@alterhome.es',
            'libreros@alterhome.es',
            'leticia@alterhome.es',
            'hosts@alterhome.es',
            'facturacion@alterhome.es',
            'eva@alterhome.es',
            'chema@alterhome.es',
            'a.blanco@alterhome.es',
            'lucas.alterhome@gmail.com',
            'dinu.alterhome@gmail.com',
            'marlon.alterhome@gmail.com',
            'alicia.alterhome@gmail.com',
            'jfederico.alterhome@gmail.com',
            'evalunaz.alterhome@gmail.com',
            'javier.alterhome@gmail.com',
            'r.alejandro.alterhome@gmail.com',
            'a.garcia.alterhome@gmail.com',
            'evaluna.alterhome@gmail.com',
            'ignacio.alterhome@gmail.com',
            'cristinar.alterhome@gmail.com',
            'jorge.alterhome@gmail.com',
            'raquel.alterhome@gmail.com',
            'juancarlos.alterhome@gmail.com',
            'tehui.alterhome@gmail.com',
            'ingrid.alterhome@gmail.com',
            'tech.user@alterhome.com',
            'housekeeping.alterhome@gmail.com',
            'leifer33@gmail.com'
        ];

        foreach ($users as $user) {
            $user = User::where('email',$user)->first();
            $user->notify(new _MailMarketing($user));
            sleep(5);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
