@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://tienda.alterhome.es/' ,'img' =>  'https://storage.googleapis.com/media-ecommerce-alterhome/public/upload/products/small_TpzlDnMkPheuvPzj7Qf6vsWwHX7gOd5oTET.jpg'])
            
        @endcomponent
    @endslot

    <div>
        <h1 style="text-align: center;font-size: 28px;">¡Completa tu compra!</h1>
        <br>
        <h2>¡Hola! {{ $user->name }}</h2>
        <p>Recuerde que tienes productos seleccionados en tu carrido de compras.</p>
        <div>
            @component('mail::table')
                |        |          |   |
                | ------------- |:-------------:| --------:|
                @foreach($products as $product)
                    | {{$product['name']}}    |  | {{$product['price']}} EUR  | 
                @endforeach
            @endcomponent
        </div>
        <div>
            @component('mail::button', ['url' => 'https://tienda.alterhome.es/shopping-cart'])
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