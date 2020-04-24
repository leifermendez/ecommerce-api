<html>
<head>
    <meta charset="UTF-8">
    <title>Restablecimiento de Contraseña</title>
</head>
<body>    
    <div>
        <br>
        <h1 style="text-align: center;font-size: 28px;">¡Hola!</h1>
        <br>
        <p>{{ $name }} usted recibió este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta</p>
        <br>
        <a href="{{ $url }}">Restablecer Contraseña</a>
        <br>
        <p>Si no solicitó un restablecimiento de contraseña, no se requiere ninguna otra acción.</p>
        <br> <br> <br> <br> <hr>
        <small>Si tiene problemas para hacer clic en el botón "Restablecer contraseña", copie y pegue la URL a continuación en su navegador web: {{ $url }}</small>
    </div>
</body>
</html>
