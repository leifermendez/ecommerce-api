@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://tienda.alterhome.es/'])
            <img src="https://storage.googleapis.com/media-ecommerce-alterhome/public/upload/products/small_TpzlDnMkPheuvPzj7Qf6vsWwHX7gOd5oTET.jpg"
                 height="40px" alt="">
        @endcomponent
    @endslot

    <div>
        <h1 style="text-align: center;font-size: 28px;">¡Bienvenido a AlterhomeShop!</h1>
        <br>
        <h2>¡Hola! {{$user->name}}</h2>
        <p>Si lo tuyo es viajar, esta es tu sección. En Alterhome Shop hemos diseñado una linea de productos pensados especialmente para los aventureros y amantes de los viajes.</p>
        <br>
        <p>Desde la comodidad de tu apartamento solamente es un click, no cuesta nada. Elige los artículos que más molan para este viaje y los souvenirs más chulos.</p>
        <br>
        <p>Con cada reserva tienes a tu disposición ofertas increíbles en muchos de nuestros productos. No te líes comienza tu lista de compras y ahorra un montón</p>
        <br>
        <p>¡Qué sí, qué si! Que la compra es segura y no almacenamos tus tarjetas, tus conexiónes bancarias son encriptadas gracias al protocolo SSL.</p>
        <div>
            <div>
                @component('mail::button', ['url' => 'https://tienda.alterhome.es/'])
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