@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">

                        <div>
                            Tu instalación se realizó con éxito, guarda los siguiente datos de conexión en un lugar seguro, no volverán a mostrarse
                        </div>
                        <div class="mt-4">
                            <b>API:</b> <code class="ml-2">{{env('APP_URL')}}/api/1.0</code>
                        </div>
                        <div class="mt-2">
                            <b>Key:</b> <code class="ml-2">{{env('APP_URL')}}/api/1.0</code>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
