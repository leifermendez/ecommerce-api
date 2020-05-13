@extends('layouts.app')
<link href="{{ asset('installer/css/custom.css') }}" rel="stylesheet"/>
@section('content')
    <div class="container dashboard-install">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h2>Paso 1</h2>
                            <div class="over-system pb-0">
                                <div class="col-12 p-0">
                                    <ul class="d-flex">
                                        @foreach($system['requirements']['php'] as $key => $field)
                                            <li class="mr-1 badge text-white p-2 {{($field) ? 'badge-success' : 'badge-danger'}}"
                                            >{{$key}}</li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            Recuerda todos los módulos deben estar en color verde para su correcto
                                            funcionamiento
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <b>Estás listo!</b> Te guiaremos mediante todo el proceso, donde vamos a ir verificando y
                            validando
                            los requerimientos del servidor. <br>
                            <br>
                            💡 <i class="text-muted">
                                Recuerda que puedes conseguir actualizaciones y material de importancia en nuestro <a
                                    href="https://www.codigoencasa.com/te-ayudamos-con-tu-codigo/"
                                    target="_blank"><b>blog</b></a>
                            </i>
                            <br>
                            👉 <i class="text-muted">Si deseas colabora con el código, o seguir más de cerca este
                                desarrollo recuerda pasarte
                                por
                                nuestros repositorios <a href="https://github.com/leifermendez/ecommerce-api"
                                                         target="_blank"><b>Visitar</b></a></i>
                        </div>
                        <hr>
                        <div>
                            <div class="mb-4">
                                🎉 Puedes ver otras tiendas realizadas con este sistema.
                            </div>
                            <div class="row col-12 p-0 mr-0 ml-0 templates mt-2">
                                <div class="col-3 block">
                                    <a href="https://tienda.mochileros.com.mx/" target="_blank">
                                        <img src="https://i.imgur.com/BjgmZLQ.png" alt="">
                                        <div class="content-card">
                                            <b class="title">Mochileros.com.mx</b>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-3 block">
                                    <a href="https://tienda.alterhome.es/" target="_blank">
                                        <img src="https://i.imgur.com/TAzbEoe.png" alt="">
                                        <div class="content-card">
                                            <b class="title">Alterhome.es</b>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-3 block">
                                    <a href="https://mercacomunidad.com/" target="_blank">
                                        <img src="https://i.imgur.com/bvSdPSK.png" alt="">
                                        <div class="content-card">
                                            <b class="title">Mercacomunidad.com</b>
                                        </div>
                                    </a>
                                </div>

                            </div>
                        </div>
                        <hr>
                        <!-- SECTION ENV -->
                        <div>

                            <form autocomplete="off" method="get" class="justify-content-between d-flex"
                                  action="{{route('InstallerWAccount')}}">
                                <button type="submit" class="btn btn-primary">Continuar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
