@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://apatxee.com/'])
            <img src="https://storage.googleapis.com/ecommerce-apatxee-v2.appspot.com/assets/logo-mail-apatxee.png"
                 height="40px" alt="">
        @endcomponent
    @endslot

    <div>
        <h1 style="text-align: center;font-size: 28px;">¡Compra realizada!</h1>
        <br>
        <h2>¡Hola! {{$purchase->name}}</h2>
        <p>Enhorabuena, tu pedido se ha realizado exitosamente.</p>
        <div>
            @component('mail::table')
                |        |          |   |
                | ------------- |:-------------:| --------:|
                @foreach ($purchase->list as $p)
                    | {{$p->products_name}} ({{$p->product_qty}})     |  | {{$p->product_amount}}      |
                @endforeach
            @endcomponent
        </div>
        <div>
            @component('mail::button', ['url' => 'https://apatxee.com/purchases'])
                Ver detalles
            @endcomponent
        </div>


    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <!-- footer here -->
@endcomponent
@endslot
@endcomponent