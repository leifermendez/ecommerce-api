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

                        <section class="mb-4 cursor-pointer" data-toggle="collapse" data-target="#collapseTemplate"
                                 aria-expanded="false" aria-controls="collapseTemplate">
                            <div>
                                <h5>Diseño</h5>
                                <small class="text-muted">
                                    A continuación puedes configurar la configuración de tus sms
                                </small>
                            </div>
                            <form autocomplete="off" method="post" class="collapse dashboard-install"
                                  id="collapseTemplate"
                                  action="{{route('AdminSaveMail')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row col-12 pl-0 pr-0 pb-1 m-0">
                                    <div class="col-12 p-0 templates d-flex mt-4">
                                        @foreach($options['templates'] as $key => $value)
                                            <div class="col-3 block">
                                                <a href="#" target="_blank">
                                                    <img src="{{$value['image']}}" alt="">
                                                    <div class="content-card">
                                                        <b class="title">{{$value['name']}}</b>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </form>

                            <hr>
                        </section>

                        <section class="mb-4 cursor-pointer" data-toggle="collapse" data-target="#collapseMail"
                                 aria-expanded="false" aria-controls="collapseMail">
                            <div>
                                <h5>Servidor de correo</h5>
                                <small class="text-muted">
                                    A continuación puedes configurar la configuración de tus sms
                                </small>
                            </div>
                            <form autocomplete="off" method="post" class="collapse" id="collapseMail"
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

                        <section class="mb-4" data-toggle="collapse" data-target="#collapseSMS"
                                 aria-expanded="false" aria-controls="collapseSMS">
                            <div>
                                <h5>Servicio de SMS</h5>
                                <small class="text-muted">Recuerda configurar tu servicio de SMS para tener habilitada
                                    esta funcionalidad</small>
                            </div>
                            <form autocomplete="off" method="post" class="collapse" id="collapseSMS"
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

                        <section class="mb-4" data-toggle="collapse" data-target="#collapseStripe"
                                 aria-expanded="false" aria-controls="collapseStripe">
                            <div>
                                <h5>Plataforma de pago</h5>
                                <small class="text-muted">Nuestra principal plataforma de pago se basa en Stripe</small>
                            </div>
                            <form autocomplete="off" method="post" class="collapse" id="collapseStripe"
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

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
