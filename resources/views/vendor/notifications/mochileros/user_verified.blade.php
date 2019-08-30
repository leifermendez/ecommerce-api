@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://tienda.mochileros.com.mx/'])
            <img src="https://media-mochileros.s3.us-east-2.amazonaws.com/upload/small_jR_SW1eGqCdQQsORSwdwv3m57W6SXrzB27Aeu9.png"
                 height="40px" alt="">
        @endcomponent
    @endslot

    <div>
        <h1 style="text-align: center;font-size: 28px;">¡Cuenta verificada!</h1>
        <br>
        <h2>¡Hola! {{$user->name}}</h2>
        <p>Enhorabuena, tu cuenta se ha verificado con éxito. Ya puedes comenzar a comprar y vender</p>
        <div>
            <div>
                @component('mail::button', ['url' => 'https://tienda.mochileros.com.mx/'])
                    ¿Ver ofertas!
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