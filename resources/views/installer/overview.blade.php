@extends('layouts.app')
<link href="{{ asset('installer/css/custom.css') }}" rel="stylesheet"/>
@section('content')
    <div class="container dashboard-install">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h2>Paso 4</h2>
                            {{--                            <div class="over-system pb-0">--}}
                            {{--                                <div class="col-12 p-0">--}}
                            {{--                                    <ul class="d-flex">--}}
                            {{--                                        @foreach($system['requirements']['php'] as $key => $field)--}}
                            {{--                                            <li class="mr-1 badge text-white p-2 {{($field) ? 'badge-success' : 'badge-danger'}}"--}}
                            {{--                                            >{{$key}}</li>--}}
                            {{--                                        @endforeach--}}
                            {{--                                    </ul>--}}
                            {{--                                    <div class="mt-1">--}}
                            {{--                                        <small class="text-muted">--}}
                            {{--                                            Recuerda todos los módulos deben estar en color verde para su correcto--}}
                            {{--                                            funcionamiento--}}
                            {{--                                        </small>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                            <hr>
                            <b>Estás listo!</b> La instalación se ha realizado exitosamente, ahora solo debes configurar
                            tus plataformas de pago<br>
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
                                💾 Recuerda guardar estos datos en un sitio seguro
                            </div>
                            <div class="row col-12 p-0 mr-0 ml-0 templates mt-2">
                                <div class="col-6  offset-md-3 text-center p-3">
                                    <h2>{{$message['user']['email']}}</h2>
                                    <h2>{{$message['user']['password']}}</h2>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <!-- SECTION ENV -->
                        <div>

                            <a href="/login" class="btn btn-primary">Continuar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
