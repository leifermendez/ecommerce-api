@extends('layouts.app')

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

                    <div>
                        <div class="alert alert-warning">
                            Proximamente
                        </div>
                    </div>

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
