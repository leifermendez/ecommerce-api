@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://tienda.mochileros.com.mx/'])
            <img src="https://media-mochileros.s3.us-east-2.amazonaws.com/upload/small_jR_SW1eGqCdQQsORSwdwv3m57W6SXrzB27Aeu9.png"
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
                    | {{$p->products_name}} ({{$p->product_qty}})     |  | {{$p->product_amount}} EUR      |
                @endforeach
            @endcomponent
        </div>
        <div>
            @component('mail::button', ['url' => 'https://tienda.mochileros.com.mx/purchases'])
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