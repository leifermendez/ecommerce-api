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

                        <section class="mb-4 cursor-pointer">
                            <div>
                                <h5>Diseño</h5>
                                <small class="text-muted">
                                    A continuación puedes configurar el diseño de tu tienda.
                                </small>
                            </div>
                            <form autocomplete="off" method="post" class="dashboard-install"
                                  action="{{route('AdminSaveTemplate')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row col-12 pl-0 pr-0 pb-1 m-0">
                                    <div class="col-12 p-0 templates d-flex mt-4">
                                        @foreach($options['templates'] as $key => $value)
                                            <div class="col-3 block">
                                                <input type="radio" name="template" required value="{{$value['url']}}">
                                                <b>{{$value['name']}}</b>
                                                <br>
                                                <img src="{{$value['image']}}" alt="">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-12 p-0 ">
                                        @foreach($options['general'] as $key => $value)
                                            <div class="form-group">
                                                <label for="form-{{$key}}">{{$value['name']}}</label>
                                                <input type="{{$value['type']}}"
                                                       name="{{$key}}"
                                                       {{($value['readonly']) ? 'readonly' : ''}}
                                                       value="{{$value['value']}}"
                                                       class="form-control  form-control-sm" id="form-{{$key}}"
                                                       placeholder="{{$value['example']}}">
                                            </div>
                                        @endforeach

                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Continuar</button>
                                    </div>
                                </div>
                            </form>

                            <hr>
                        </section>

                        <section class="mb-4 cursor-pointer">
                            <div  data-toggle="collapse" data-target="#collapseMail"
                                  aria-expanded="false" aria-controls="collapseMail">
                                <h5>Servidor de correo</h5>
                                <small class="text-muted">
                                    A continuación puedes configurar los ajustes de tu servidor mail
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

                        <section class="mb-4">
                            <div data-toggle="collapse" data-target="#collapseSMS"
                                 aria-expanded="false" aria-controls="collapseSMS">
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

                        <section class="mb-4">
                            <div  data-toggle="collapse" data-target="#collapseStripe"
                                  aria-expanded="false" aria-controls="collapseStripe">
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
