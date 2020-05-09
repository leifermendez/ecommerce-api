@extends('layouts.app')
<link href="{{ asset('installer/css/custom.css') }}" rel="stylesheet"/>
@section('content')
    <div class="container dashboard-install">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div>
                            ¡Tu sitio web ya está listo, ahora solo debes descargar <code>awesome-website.zip</code>
                            luego subir los
                            archivos en tu hosting y listo! <br>
                            Recuerda si tienes dudas o requieres ayuda estamos disponible en nuestro grupo de <a
                                href="https://www.facebook.com/groups/163216871776185/" target="_blank">facebook</a>
                        </div>
                        <div>
                            Apoya este proyecto dejando "estrella" en el repositorio y compartiendo
                            <a href="https://github.com/leifermendez/ecommerce-api" target="_blank">https://github.com/leifermendez/ecommerce-api</a>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="/awesome-website.zip" target="_blank" class="btn btn-lg btn-primary">Descargar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
