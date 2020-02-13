@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://tienda.alterhome.es/' ,'img' =>  'https://storage.googleapis.com/media-ecommerce-alterhome/public/upload/products/small_TpzlDnMkPheuvPzj7Qf6vsWwHX7gOd5oTET.jpg'])
            
        @endcomponent
    @endslot

    <div>
        <h1 style="text-align: center;font-size: 28px;">¡Reporte de Compras!</h1>
        <br>
        <h2>¡Hola!</h2>
        <p>Resporte de compras adjunto</p>

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <!-- footer here -->
@endcomponent
@endslot
@endcomponent