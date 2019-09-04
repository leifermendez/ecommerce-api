@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', [
            'url' => 'https://apatxee.com/',
            'img' => 'https://storage.googleapis.com/ecommerce-apatxee-v2.appspot.com/assets/logo-mail-apatxee.png'])
            <img src="https://storage.googleapis.com/ecommerce-apatxee-v2.appspot.com/assets/logo-mail-apatxee.png"
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
                @component('mail::button', ['url' => 'https://apatxee.com/'])
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