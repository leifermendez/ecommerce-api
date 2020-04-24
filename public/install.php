<?php
set_time_limit(0);
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */
define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/
require __DIR__ . '/../vendor/autoload.php';
/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/
$app = require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/
$cURL = false;
$curl_init = false;
$phpversion = false;
$PDO = false;
$gd_info = false;
$exif = false;
$mbstring = false;
$zip = false;
if (function_exists('curl_init') === false) {
    $cURL = false;
} else {
    $cURL = true;
}

if (function_exists('gd_info') === false) {
    $gd_info = false;
} else {
    $gd_info = true;
}
if (!extension_loaded('mbstring')) {
    $mbstring = false;
} else {
    $mbstring = true;
}

if (!extension_loaded('EXIF')) {
    $exif = false;
} else {
    $exif = true;
}
if (!extension_loaded('ZIP')) {
    $zip = false;
} else {
    $zip = true;
}

if ((phpversion()) >= 7.2) {
    $phpversion = true;
}

$host = isset($_POST['host']) ? $_POST['host'] : null;
$dbname = isset($_POST['dbname']) ? $_POST['dbname'] : null;
$username = isset($_POST['username']) ? $_POST['username'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

$id_stripe = isset($_POST['idstripe']) ? $_POST['idstripe'] : null;
$sk = isset($_POST['sk']) ? $_POST['sk'] : null;
$pk = isset($_POST['pk']) ? $_POST['pk'] : null;
$sk_sandbox = isset($_POST['sk_sambox']) ? $_POST['sk_sambox'] : null;
$pk_sandbox = isset($_POST['pk_sambox']) ? $_POST['pk_sambox'] : null;
$path_dir = dirname(__FILE__, 2);
$command = ' php ' . $path_dir . '/artisan initialize:send --DB_HOST=' . $host . ' --DB_PASS=' . $password . ' --DB_USER=' . $username . ' --DB_NAME=' . $dbname . ' --STRIPE_ID=' . $id_stripe . ' --STRIPE_SK=' . $sk . ' --STRIPE_PK=' . $pk . ' --STRIPE_SANDBOX_PK=' . $pk_sandbox . ' --STRIPE_SANDBOX_SK=' . $sk_sandbox;
$install = false;

if (isset($host) && isset($dbname) && isset($username) && isset($id_stripe) && isset($sk)
    && isset($pk) && isset($sk_sandbox) && isset($pk_sandbox)) {
    $exit_string = shell_exec($command);

    $command_string = $exit_string;
    $math_string = 'complete_success_system';
    $pos = strpos($command_string, $math_string);
    if ($pos === false) {
        echo "Algo salio mal";
        echo "<pre>$exit_string</pre>";
    } else {
        $install = true;
    }
//    echo "<pre>$exit_string</pre>";
}


?>
<html>
<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body class="bg-light">
<?php
if ($install) {
    ?>
    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg"
                 alt="" width="72" height="72">
            <h2>Instalación completada</h2>
            <p class="lead">Tu tienda ya tiene su api instalada correctamente.</p>
        </div>
    </div>
    <?php
}
?>
<?php
if (!$install) {
    ?>
    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4"
                 src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72"
                 height="72">
            <h2>INSTALACIÓN</h2>
            <p class="lead">Para que funcione tu Tienda online correctamente, te recomendamos llenar todos lo campos
                y verficar que cumples
                con los requerimientos del sistema.</p>
        </div>

        <div class="row">
            <div class="col-md-4 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Requerimientos</span>
                    <span class="badge badge-secondary badge-pill">5</span>
                </h4>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0">PHP Version</h6>
                            <small class="text-muted">version minima de PHP >= 7.2</small>
                        </div>
                        <span class="text-muted"><?php echo $phpversion ? 'Completo' : 'Error' ?> </span>
                    </li>
                    <!--                    <li class="list-group-item d-flex justify-content-between lh-condensed">-->
                    <!--                        <div>-->
                    <!--                            <h6 class="my-0">Mysql</h6>-->
                    <!--                            <small class="text-muted">version minima de MySql >= 5.8</small>-->
                    <!--                        </div>-->
                    <!--                        <span class="text-muted">Completo</span>-->
                    <!--                    </li>-->
                    <!--                    <li class="list-group-item d-flex justify-content-between lh-condensed">-->
                    <!--                        <div>-->
                    <!--                            <h6 class="my-0">MariaDB</h6>-->
                    <!--                            <small class="text-muted">Version minima de MariaDB >= 10.2</small>-->
                    <!--                        </div>-->
                    <!--                        <span class="text-muted">Completo</span>-->
                    <!--                    </li>-->
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0">cURL</h6>
                            <small class="text-muted">Version minima de cURL >= 7.19.4+</small>
                        </div>
                        <span class="text-muted"><?php echo $cURL ? 'Completo' : 'Error' ?>  </span>
                    </li>

                    <!--                    <li class="list-group-item d-flex justify-content-between">-->
                    <!--                        <strong>GD</strong>-->
                    <!--                        <span class="text-muted">-->
                    <?php //echo $gb ? 'Completo' : 'Error' ?><!--  Completo</span>-->
                    <!--                    </li>-->

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>mbstring</strong>
                        <span class="text-muted"><?php echo $mbstring ? 'Completo' : 'Error' ?>  </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>EXIF</strong>
                        <span class="text-muted"><?php echo $exif ? 'Completo' : 'Error' ?>  </span>

                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <strong>ZIP</strong>
                        <span class="text-muted"><?php echo $zip ? 'Completo' : 'Error' ?>  </span>
                    </li>

                    <!--                    <li class="list-group-item d-flex justify-content-between">-->
                    <!--                        <strong>allow_url_fopen</strong>-->
                    <!--                        <span>Completo</span>-->
                    <!--                    </li>-->
                </ul>

                <!--                <form class="card p-2">-->
                <!--                    <div class="input-group">-->
                <!--                        <input type="text" class="form-control" placeholder="Promo code">-->
                <!--                        <div class="input-group-append">-->
                <!--                            <button type="submit" class="btn btn-secondary">Redeem</button>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </form>-->
            </div>
            <div class="col-md-8 order-md-1">
                <h4 class="mb-3">Datos de Conexión</h4>
                <form name="formulario" method="POST" class="needs-validation" novalidate="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="host">Database host <span class="text-muted">127.0.0.1</span></label>
                            <input value="<?php echo $host ?>" name="host" type="text" class="form-control"
                                   placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="database">Database name</label>
                            <input value="<?php echo $dbname ?>" name="dbname" type="text" class="form-control"
                                   id="lastName" placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username">Username <span class="text-muted">root</span></label>
                            <input value="<?php echo $username ?>" name="username" type="text" class="form-control"
                                   placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label name="password" for="password">Password <span class="text-muted">(Vacio si no tienes clave)</span></label>
                            <input value="<?php echo $password ?>" type="password" class="form-control"
                                   placeholder="">
                        </div>
                    </div>

                    <hr class="mb-4">
                    <h4 class="mb-3">Datos de pago (STRIPE)</h4>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="host">Id stripe <span class="text-muted"></span></label>
                            <input value="<?php echo $id_stripe ?>" name="idstripe" type="text" class="form-control"
                                   placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="database">Clave secreta <span class="text-muted">SK_xxxxxxx</span></label>
                            <input value="<?php echo $sk ?>" name="sk" type="text" class="form-control"
                                   id="lastName" placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="database">Clave publica <span class="text-muted">PK_xxxxxxx</span></label>
                            <input value="<?php echo $pk ?>" name="pk" type="text" class="form-control"
                                   id="lastName" placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                    </div>
                    <hr class="mb-4">
                    <h4 class="mb-3">Datos de pago (STRIPE) Modo desarrollador</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username">Clave secreta <span class="text-muted">SAMBOX</span></label>
                            <input value="<?php echo $sk_sandbox ?>" name="sk_sambox" type="text"
                                   class="form-control" placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username">Clave publica <span class="text-muted">SAMBOX</span></label>
                            <input value="<?php echo $pk_sandbox ?>" name="pk_sambox" type="text"
                                   class="form-control" placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4">
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Continiar con la instalación
                    </button>
                </form>
            </div>
        </div>

        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">© 2017-2020 LeangaSoftware</p>
            <!--            <ul class="list-inline">-->
            <!--                <li class="list-inline-item"><a href="#">Privacy</a></li>-->
            <!--                <li class="list-inline-item"><a href="#">Terms</a></li>-->
            <!--                <li class="list-inline-item"><a href="#">Support</a></li>-->
            <!--            </ul>-->
        </footer>
    </div>
    <?php
}
?>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
<script src="../../assets/js/vendor/popper.min.js"></script>
<script src="../../dist/js/bootstrap.min.js"></script>
<script src="../../assets/js/vendor/holder.min.js"></script>
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function () {
        'use strict';

        window.addEventListener('load', function () {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');

            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
</body>
</html>
