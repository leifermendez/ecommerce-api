<?php

namespace Tests\Feature;

use DB;
use App\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersTest extends TestCase
{
    use RefreshDatabase;
    /**
     @test
     */
    public function usuario_solicita_recuperar_clave(){
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create(['email' => 'admin@mail.com']);
        $response = $this->get('api/admin/auth/reset_password/admin@mail.com');
        $this->assertDatabaseHas('password_resets',['email'=>'admin@mail.com']);
        $response->assertStatus(200)
        ->assertJson(
            ['status' => 'success',
            'msg' => 'Email enviado',
            'code' => 0]
        );
        $data = DB::table('password_resets')->select('token')->where('email', 'admin@mail.com')->first();
        return $data->token;
    }

    /**
     @test
     */
    public function usuario_con_token_valido(){
        $token = $this->usuario_solicita_recuperar_clave();
        $response = $this->post('api/admin/auth/reset_password', ['token' =>$token,'password'=>'secret']);
        $response->assertStatus(200)
        ->assertJson([
                'status' => 'success',
                'msg' => 'Password actualizado',
                'code' => 0
            ]);        
    }

    /**
     @test
     */
    public function usuario_con_token_no_valido(){
        $response = $this->post('api/admin/auth/reset_password', ['token' =>'123456','password'=>'secret']);
        $response->assertStatus(401)
        ->assertJson([
                'status' => 'fail',
                'msg' => 'Token no existe o ya fue utilizado',
                'code' => 1
                ]);        
    }
    /**
     * @test 
     */
    function login_correcto(){
        $user = factory(User::class)->create(['email' => 'admin@mail.com']);
        $response = $this->json('POST', '/api/admin/auth/login', ["email"=>"admin@mail.com","password"=>"password"]);
        $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                "id",
                "name",
                "email",
                "email_verified_at",
                "phone",
                "role",
                "status",
                "confirmed",
                "avatar",
                "header",
                "referer_code",
                "created_at",
                "updated_at",
                "deleted_at",
                "token"
            ],
            'code'
        ]);
    }

    /**
     * @test 
     */
    function login_password_errada()
    {
        $user = factory(User::class)->create(['email' => 'admin@mail.com']);
        $response = $this->json('POST', '/api/admin/auth/login', ["email"=>"admin@mail.com","password"=>"error"]);
        $response->assertStatus(401)
        ->assertJsonStructure(['error']);
    }

    /**
     * @test 
     */
    function login_email_errado()
    {
        $user = factory(User::class)->create(['email' => 'admin@mail.com']);
        $response = $this->json('POST', '/api/admin/auth/login', ["email"=>"other@mail.com","password"=>"password"]);
        $response->assertStatus(401)
        ->assertJsonStructure(['error']);
    }

    /**
     * crear usuario admin y loguarse
     * @return token
     */
    public function getAdminToken(){
        $user = factory(User::class)->create(['email' => 'admin@mail.com','role'=> 'admin']);
        $response = $this->json('POST', '/api/admin/auth/login', ["email"=>"admin@mail.com","password"=>"password"]);
        $res = json_decode($response->getContent());

        return $res->data->token;
    }

    /**
     * @test 
     */
    function listar_usuarios_con_user_admin_auth()
    {
        $token = $this->getAdminToken();
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('GET', '/api/admin/user');

        $response->assertStatus(200)
        ->assertJsonStructure([
            "status",
            "data" => [
                "current_page",
                "data"=> [
                    [
                        "id",
                        "name",
                        "email",
                        "email_verified_at",
                        "phone",
                        "role",
                        "status",
                        "confirmed",
                        "avatar",
                        "header",
                        "referer_code",
                        "created_at",
                        "updated_at",
                        "deleted_at"
                    ],
                ],
                "first_page_url",
                "from",
                "last_page",
                "last_page_url",
                "next_page_url",
                "path",
                "per_page",
                "prev_page_url",
                "to",
                "total"
            ],
            "code"
        ]);
    }

    /**
     * @test 
     */
    function listar_usuarios_con_user_admin_sin_auth()
    {
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . ''),
        ])->json('GET', '/api/admin/user');
        $response->assertStatus(403);
    }

    /**
     * @test 
     */
    function usuario_admin_auth_crear_usuario()
    {
        $token = $this->getAdminToken();
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('POST', '/api/admin/user', [
            "name"=>"Jose Torrealba",
            "email"=>"box1488@gmail.com",
            "password"=>123456,
            "phone"=>"(555)55-555-55",
            "role"=>"admin",
            "avatar"=>"http://lorempixel.com/640/480/",
            "header"=>"http://lorempixel.com/640/480/",
            "status"=>"available"
        ]);

        $response->assertStatus(200)
        ->assertJsonStructure([
            "status",
            "data"=> [
                "id",
                "name",
                "email",
                "email_verified_at",
                "phone",
                "role",
                "status",
                "confirmed",
                "avatar",
                "header",
                "referer_code",
                "created_at",
                "updated_at",
                "deleted_at",
                "token"
            ],
            "code"
        ]);
    }

    /**
     * @test 
     */
    function usuario_admin_auth_crear_usuario_que_existe()
    {
        $token = $this->getAdminToken();
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('POST', '/api/admin/user', [
            "name"=>"Jose Torrealba",
            "email"=>"admin@mail.com",
            "password"=>123456,
            "phone"=>"(555)55-555-55",
            "role"=>"admin",
            "avatar"=>"http://lorempixel.com/640/480/",
            "header"=>"http://lorempixel.com/640/480/",
            "status"=>"available"
        ]);

        $response->assertStatus(401)
        ->assertJsonStructure([
            'status',
            'msg',
            'code'
        ]);
    }

    /**
     * @test 
     */
    function usuario_admin_consulta_un_usuario_existente()
    {
        $user = factory(User::class)->create(['email' => 'admin@mail.com','role'=> 'admin']);
        $response = $this->json('POST', '/api/admin/auth/login', ["email"=>"admin@mail.com","password"=>"password"]);
        $res = json_decode($response->getContent());

        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $res->data->token),
        ])->json('GET', ('/api/admin/user/'.$res->data->id));
        $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                "id",
                "name",
                "email",
                "email_verified_at",
                "phone",
                "role",
                "status",
                "confirmed",
                "avatar",
                "header",
                "referer_code",
                "created_at",
                "updated_at",
                "deleted_at",
            ],
            'code'
        ]);
    }

    /**
     * @test 
     */
    function usuario_admin_consulta_un_usuario_que_no_existe()
    {
        $token = $this->getAdminToken();
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('GET', ('/api/admin/user/100'));
        $response->assertStatus(200)
        ->assertJsonStructure([
            "status",
            "data",
            "code"
        ]);
    }

    /**
     * @test 
     */
    function usuario_admin_edita_un_usuario_existente()
    {
        $token = $this->getAdminToken();

        $data = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('POST', '/api/admin/user', [
            "name"=>"Jose Torrealba",
            "email"=>"box1488@gmail.com",
            "password"=>123456,
            "phone"=>"(555)55-555-55",
            "role"=>"admin",
            "avatar"=>"http://lorempixel.com/640/480/",
            "header"=>"http://lorempixel.com/640/480/",
            "status"=>"available"
        ]);

        $res = json_decode($data->getContent());
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('PUT', ('/api/admin/user/'.$res->data->id), [
            "name"=>"XXXXXXXX",
            "email"=>"dddddd@deded.com_",
            "password"=>1234567,
            "phone"=>"(555)55-555-55",
            "role"=>"admin",
            "avatar"=>"http://lorempixel.com/640/480/",
            "header"=>"http://lorempixel.com/640/480/",
            "status"=>"available"
        ]);
        $response->assertStatus(200)
        ->assertJsonStructure([
            "status",
            "msg",
            "data" => [
                "id",
                "name",
                "email",
                "email_verified_at",
                "phone",
                "role",
                "status",
                "confirmed",
                "avatar",
                "header",
                "referer_code",
                "created_at",
                "updated_at",
                "deleted_at"
            ],
            "code"
        ]);
    }

    /**
     * @test 
     */
    function usuario_admin_edita_un_usuario_no_existente()
    {
        $token = $this->getAdminToken();
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('PUT', ('/api/admin/user/8'), [
            "name"=>"XXXXXXXX",
            "email"=>"dddddd@deded.com_",
            "password"=>1234567,
            "phone"=>"(555)55-555-55",
            "role"=>"admin",
            "avatar"=>"http://lorempixel.com/640/480/",
            "header"=>"http://lorempixel.com/640/480/",
            "status"=>"available"
        ]);

        $response//->assertStatus(400)
        ->assertJsonStructure([
            "status",
            "msg",
            "code"
        ]);
    }

    /**
     * @test 
     */
    function usuario_admin_edita_su_propio_registro()
    {
        $user = factory(User::class)->create(['email' => 'admin@mail.com','role'=> 'admin']);
        $data = $this->json('POST', '/api/admin/auth/login', ["email"=>"admin@mail.com","password"=>"password"]);
        $res = json_decode($data->getContent());

        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $res->data->token),
        ])->json('PUT', ('/api/admin/user/'.$res->data->id), [
            "name"=>"XXXXXXXX",
            "email"=>"dddddd@deded.com_",
            "password"=>1234567,
            "phone"=>"(555)55-555-55",
            "role"=>"admin",
            "avatar"=>"http://lorempixel.com/640/480/",
            "header"=>"http://lorempixel.com/640/480/",
            "status"=>"available"
        ]);
        $response//->assertStatus(400)
        ->assertJsonStructure([
            "status",
            "msg",
            "code"
        ]);
    }
    
    /**
     * @test 
     */
    function usuario_admin_edita_un_usuario_sin_enviar_email()
    {
        $token = $this->getAdminToken();
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('PUT', ('/api/admin/user/8'), [
            "name"=>"XXXXXXXX",
            "password"=>1234567,
            "phone"=>"(555)55-555-55",
            "role"=>"admin",
            "avatar"=>"http://lorempixel.com/640/480/",
            "header"=>"http://lorempixel.com/640/480/",
            "status"=>"available"
        ]);
        $response->assertStatus(500)
        ->assertJsonStructure([
            "status",
            "msg",
            "code"
        ]);
    }

    /**
     * @test 
     */
    function usuario_admin_elimina_usuario_existente()
    {
        $user = factory(User::class)->create(['email' => 'admin@mail.com','role'=> 'admin']);
        $data = $this->json('POST', '/api/admin/auth/login', ["email"=>"admin@mail.com","password"=>"password"]);
        $res = json_decode($data->getContent());

        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $res->data->token),
        ])->json('DELETE', ('/api/admin/user/'.$res->data->id));
        $response->assertStatus(200)
        ->assertJsonStructure([
            "status",
            "msg",
            "data" => [
                "id",
                "name",
                "email",
                "email_verified_at",
                "phone",
                "role",
                "status",
                "confirmed",
                "avatar",
                "header",
                "referer_code",
                "created_at",
                "updated_at",
                "deleted_at"
            ],
            "code"
        ]);
    }    

    /**
     * @test 
     */
    function usuario_admin_elimina_usuario_que_no_existe()
    {
        $token = $this->getAdminToken();
        $response = $this->withHeaders([
            'Authorization'=> ('Bearer ' . $token),
        ])->json('DELETE', ('/api/admin/user/1000'));
        $response->assertStatus(500);
    }

}

