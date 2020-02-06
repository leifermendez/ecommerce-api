<html>
<head>
    <meta charset="UTF-8">
    <title>Completa tu compra</title>
</head>
<body>    
    <div>
        <br>
        <h1 style="text-align: center;font-size: 28px;">¡Hola!</h1>
        <br>
        <p>Recuerde que tienes productos seleccionados en tu carrido de compras</p>
        <p>nombre:{{ $user->name }} </p>
        <p>Apellido:{{ $user->apaellido }} </p>
        <p>Avatar:{{ $user->avatar }} </p>
        <p>productos en carrito:</p>
        <ul>
        	@foreach($products as $product)
        	<li>{{$product['name']}} | {{$product['price']}} | {{$product['url']}}</li>
        	@endforeach
    	</ul>
    </div>
</body>
</html>