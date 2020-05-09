@extends('layouts.app')
<link href="{{ asset('installer/css/custom.css') }}" rel="stylesheet"/>
@section('content')
    <div class="container dashboard-install">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            Tu instalación se realizó con éxito, guarda los siguiente datos de conexión en un lugar
                            seguro, no volverán a mostrarse
                        </div>
                        <div class="mt-4">
                            <b>API:</b> <code class="ml-2">{{$data['url']}}</code>
                        </div>
                        <div class="mt-2">
                            <b>Key:</b> <code class="ml-2">{{$data['token']->token}}</code>
                        </div>
                        <div class="mt-2">
                            <b>User:</b> <code class="ml-2">{{$data['user']['email']}}</code>
                        </div>
                        <div class="mt-2">
                            <b>Password:</b> <code class="ml-2">{{$data['user']['password']}}</code>
                        </div>
                        <div class="alert alert-warning mt-2">
                            <b>Atención</b>, recuerda guardar en un lugar seguro tu usuario y contraseña de
                            administrador.
                        </div>
                        <hr>
                        <!-- SECTION TEMPLATE -->
                        <div>
                            <div class="mb-4">
                                Selecciona uno de los siguientes templates disponibles.
                            </div>
                            <div class="row col-12 p-0 mr-0 ml-0 templates mt-2">
                                <div class="col-3 block">
                                    <img src="https://i.imgur.com/BjgmZLQ.png" alt="">
                                    <div class="content-card">
                                        <b class="title">Template 1</b>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <hr>
                        <!-- SECTION ENV -->
                        <div>
                            <div class="mb-4">
                                Para continuar con la instalación recuerda ingresar y verificar los datos de conexión
                                con tu API
                            </div>
                            <form autocomplete="off" method="post"
                                  action="{{ route('LaravelUpdater::finishInstaller') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="template" value="1">

                                <div class="form-group">
                                    <label for="apiSrc">API:</label>
                                    <input type="url" name="apiSrc" value="{{$data['url']}}" required
                                           class="form-control" id="apiSrc"
                                           placeholder="">
                                    <small id="apiSrcHelp" class="form-text text-muted">Ingresa la url de tu
                                        api.</small>
                                </div>

                                <div class="form-group">
                                    <label for="apiKey">Key:</label>
                                    <input type="text" name="apiKey" value="{{$data['token']->token}}" required
                                           class="form-control" id="apiKey"
                                           placeholder="">
                                    <small id="apiKeyHelp" class="form-text text-muted">Ingresa la key.</small>
                                </div>

                                @foreach($data['fields'] as $value)
                                    <div class="form-group">
                                        <label for="{{$value['name']}}">{{$value['label']}}:</label>
                                        <input type="{{$value['type']}}" name="{{$value['name']}}" required
                                               class="form-control" value="{{$value['value']}}"
                                               id="{{$value['name']}}" placeholder="">
                                        <small class="form-text text-muted">{{$value['help']}}</small>
                                    </div>
                                @endforeach
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
