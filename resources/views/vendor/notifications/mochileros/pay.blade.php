@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://tienda.mochileros.com.mx/'])
            <img src="https://media-mochileros.s3.us-east-2.amazonaws.com/upload/small_jR_SW1eGqCdQQsORSwdwv3m57W6SXrzB27Aeu9.png"
                 height="40px" alt="">
        @endcomponent
    @endslot

    <div>
        <h1 style="text-align: center;font-size: 28px;">¡Pago exitoso!</h1>
        <br>
        <h2>¡Hola! {{$pay->name}}</h2>
        <p>Tu pago se ha procesado de forma exitosa, y tu compra fue enviada</p>
        <p>Referencia:</p>
        <h1>{{$pay->uuid}}</h1>
        <div>
            <div>
                @component('mail::button', ['url' => 'https://tienda.mochileros.com.mx/purchases'])
                    Ver detalles
                @endcomponent
            </div>
        </div>


    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <!-- footer here -->
@endcomponent
@endslot
@endcomponent