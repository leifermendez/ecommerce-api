@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://apatxee.com/'])
            <img src="https://storage.googleapis.com/ecommerce-apatxee-v2.appspot.com/assets/logo-mail-apatxee.png"
                 height="40px" alt="">
        @endcomponent
    @endslot

    <div>
        <h1 style="text-align: center;font-size: 28px;">¡Bienvenido a la apatxee!</h1>
        <br>
        <h2>¡Hola! {{$user->name}}</h2>
        <p>Ayudamos al pequeño comercio a competir con las grandes superficies con un transporte del producto con
            recogida y entrega en un tiempo medio de 1 hora.</p>
        <br>
        <p>Fomentamos la economía local porque los productos se compran y se venden en tu misma ciudad fomentando la
            economía local y el comercio de cercanía.</p>
        <br>
        <p>Transporte verde porque los productos se entregan a una distancia máxima de 10 Km y así colaboramos con la
            reducción de contaminantes a la atmósfera.</p>
        <br>
        <p>Si compras en nuestra plataforma estas colaborando con la supervivencia de las pequeñas empresas entregando
            el dinero a muchos pequeños comercios.</p>
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