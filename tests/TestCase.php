<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    //Rol => ['admin','user','seller','shop']
    public function getAdminToken(){
        $user = factory(User::class)->create(['email' => 'admin@mail.com','role'=> 'admin']);
        $response = $this->json('POST', '/api/admin/auth/login', ["email"=>"admin@mail.com","password"=>"password"]);
        $res = json_decode($response->getContent());
        return $res->data->token;
    }
}
