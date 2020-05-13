@extends('layouts.app')
<link href="{{ asset('installer/css/custom.css') }}" rel="stylesheet"/>
@section('content')
    <div class="container dashboard-install">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h2>Paso 3</h2>
                            <div class="over-system pb-0">
                                <div class="col-12 p-0">
                                    {{--                                    <ul class="d-flex">--}}
                                    {{--                                        @foreach($permissions['permissions'] as $field)--}}
                                    {{--                                            <li class="mr-1 badge text-white p-2 {{($field['isSet']) ? 'badge-success' : 'badge-danger'}}"--}}
                                    {{--                                            >{{$field['folder']}} ({{$field['permission']}})--}}
                                    {{--                                            </li>--}}
                                    {{--                                        @endforeach--}}
                                    {{--                                    </ul>--}}
                                    {{--                                    <div class="mt-1">--}}
                                    {{--                                        <small class="text-muted">--}}
                                    {{--                                            Recuerda los siguientes directorios deben tener el permiso 775 establecido--}}
                                    {{--                                            para su correcto funcionamiento--}}
                                    {{--                                        </small>--}}
                                    {{--                                    </div>--}}

                                </div>
                            </div>
                            <hr>
                            <b>Estás listo!</b> Comenzaremos a ejecutar magia con el servidor, de esta forma todo estará
                            listo en un momento! no actualices ni refresques la página <br>
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
                        <!-- SECTION ENV -->
                        <div>
                            <form autocomplete="off" method="post" class=""
                                  action="{{route('InstallerMigrationsSave')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row col-12 pl-0 pr-0 pb-4 m-0">
                                    <div class="col-6">
                                        @foreach($form as $key => $value)
                                            <div class="form-group">
                                                <label for="form-{{$key}}">{{$value['name']}}</label>
                                                <input type="{{$value['type']}}"
                                                       name="{{$key}}"
                                                       required
                                                       class="form-control" id="form-{{$key}}"
                                                       placeholder="{{$value['example']}}">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <img class="img-loading-db" src="{{ asset('installer/img/db.gif') }}"
                                                 alt="">
                                        </div>
                                    </div>
                                </div>
                                <hr>


                                <a href="{{route('InstallerWAccount')}}" class="btn btn-outline-primary">Volver</a>
                                <button type="submit" class="btn btn-primary">Continuar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
