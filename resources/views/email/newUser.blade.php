@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="https://s3.us-east-2.amazonaws.com/media-mochileros/asset/logo.png" height="40px" alt="">
        @endcomponent
    @endslot

    <div>
        <div>
            <img src="https://s3.us-east-2.amazonaws.com/media-mochileros/asset/welcome.png" alt="">
        </div>
        <br>
        <h1 style="text-align: center;font-size: 28px;">¡Bienvenido a la comunidad!</h1>
        <br>
        <h2>¡Hola! {{$user->name}}</h2>
        <p>Bienvenid@ a la comunidad de viajeros más grande de Latinoamérica.
            Estamos felices de saber que te unes. En mochileros creemos que cuando los viajes
            se comparten, las aventuras saben mejor. Somos jóvenes apasionados
            por viajar de una forma auténtica, sin prisa. Tenemos un mundo que descubrir y
            nosotros queremos acompañarte a conocerlo, nuestra experiencia nos permite ofrecerte
            rutas no convencionales que te harán vivir experiencias únicas durante tu travesía.</p>
        <div>
            @component('mail::button', ['url' => 'https://mochileros.com.mx'])
                ¿Cuál será tu próxima aventura?
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