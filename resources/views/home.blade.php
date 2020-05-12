@extends('layouts.app')
<link href="{{ asset('installer/css/custom.css') }}" rel="stylesheet"/>
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <section class="mb-4">
                            <div>
                                <h4>Servidor de correo</h4>
                                <small class="text-muted">
                                    A continuación puedes configurar la configuración de tus sms
                                </small>
                            </div>
                            <form autocomplete="off" method="post" class=""
                                  action="{{route('AdminSaveMail')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row col-12 pl-0 pr-0 pb-1 m-0">
                                    <div class="col-6 p-0">
                                        @foreach($options['mail'] as $key => $value)
                                            <div class="form-group">
                                                <label for="form-{{$key}}">{{$value['name']}}</label>
                                                <input type="{{$value['type']}}"
                                                       name="{{$key}}"
                                                       value="{{$value['value']}}"
                                                       class="form-control  form-control-sm" id="form-{{$key}}"
                                                       placeholder="{{$value['example']}}">
                                            </div>
                                        @endforeach
                                        <div>
                                            <button type="submit" class="btn btn-primary">Continuar</button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted">
                                            La siguiente configuración es importante para el buen funcionamiento del
                                            envió de tus mails
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <hr>
                        </section>

                        <section class="mb-4">
                            <div>
                                <h4>Servicio de SMS</h4>
                            </div>
                            <form autocomplete="off" method="post" class=""
                                  action="{{route('AdminSaveSMS')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row col-12 pl-0 pr-0 pb-1 m-0">
                                    <div class="col-6 p-0">
                                        @foreach($options['sms'] as $key => $value)
                                            <div class="form-group">
                                                <label for="form-{{$key}}">{{$value['name']}}</label>
                                                <input type="{{$value['type']}}"
                                                       name="{{$key}}"
                                                       value="{{$value['value']}}"
                                                       class="form-control  form-control-sm" id="form-{{$key}}"
                                                       placeholder="{{$value['example']}}">
                                            </div>
                                        @endforeach
                                        <div>
                                            <button type="submit" class="btn btn-primary">Continuar</button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted">
                                            La siguiente configuración es importante para el buen funcionamiento del
                                            envió de tus mails
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                        </section>

                        <section class="mb-4">
                            <div>
                                <h4>Plataforma de pago</h4>
                            </div>
                            <form autocomplete="off" method="post" class=""
                                  action="{{route('AdminSaveStripe')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row col-12 pl-0 pr-0 pb-1 m-0">
                                    <div class="col-6 p-0">
                                        @foreach($options['stripe'] as $key => $value)
                                            <div class="form-group">
                                                <label for="form-{{$key}}">{{$value['name']}}</label>
                                                <input type="{{$value['type']}}"
                                                       name="{{$key}}"
                                                       value="{{$value['value']}}"
                                                       class="form-control  form-control-sm" id="form-{{$key}}"
                                                       placeholder="{{$value['example']}}">
                                            </div>
                                        @endforeach
                                        <div>
                                            <button type="submit" class="btn btn-primary">Continuar</button>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted">
                                            La siguiente configuración es importante para el buen funcionamiento de la
                                            plataforma de pago
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                        </section>

                        {{--                        <div class="col-12 p-0 r-0 row">--}}
                        {{--                            <div class="col-3">--}}
                        {{--                                <div class="card">--}}
                        {{--                                    <img class="card-img-top" src="..." alt="Card image cap">--}}
                        {{--                                    <div class="card-body">--}}
                        {{--                                        <h5 class="card-title">Card title</h5>--}}
                        {{--                                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>--}}
                        {{--                                        <a href="#" class="btn btn-primary">Go somewhere</a>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="col-3">--}}
                        {{--                                <div class="card">--}}
                        {{--                                    <img class="card-img-top" src="..." alt="Card image cap">--}}
                        {{--                                    <div class="card-body">--}}
                        {{--                                        <h5 class="card-title">Card title</h5>--}}
                        {{--                                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>--}}
                        {{--                                        <a href="#" class="btn btn-primary">Go somewhere</a>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}



                        {{--                        </div>--}}

                        {{--                    You are logged in!--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
