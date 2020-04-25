<?php
set_time_limit(0);

/**
 * Defined settings
 */
class InstallPackage
{
    protected $MODULES;
    static $PHP_VERSION = 7.1;
    protected $PATH;
    protected $DATABASE = array(
        'HOST' => null,
        'USER' => null,
        'PASSWORD' => null,
        'NAME' => null
    );
    protected $COMMAND = array();
    protected $ALL_CHECK = array();
    protected $NOTICE = array();

    public function getConnect()
    {
        return $this->DATABASE;
    }

    public function __construct()
    {
        $this->PATH = dirname(__FILE__, 2);
        $this->MODULES = [
            'PHP_VERSION' => [
                'label' => 'PHP',
                'version' => self::$PHP_VERSION,
                'required' => true,
                'pass' => null,
                'function' => call_user_func(function () {
                    return ((phpversion()) >= self::$PHP_VERSION);
                })
            ],
            'CURL' => [
                'label' => 'cURL',
                'required' => true,
                'pass' => null,
                'function' => call_user_func(function () {
                    return (function_exists('curl_init') !== false);
                })
            ],
            'GD' => [
                'label' => 'cURL',
                'required' => true,
                'pass' => null,
                'function' => call_user_func(function () {
                    return (function_exists('gd_info') !== false);
                })
            ],
            'mbstring' => [
                'label' => 'mbstring',
                'required' => true,
                'pass' => null,
                'function' => call_user_func(function () {
                    return (extension_loaded('mbstring'));
                })
            ],
            'EXIF' => [
                'label' => 'EXIF',
                'required' => true,
                'pass' => null,
                'function' => call_user_func(function () {
                    return (extension_loaded('EXIF'));
                })
            ],
            'ZIP' => [
                'label' => 'ZIP',
                'required' => true,
                'pass' => null,
                'function' => call_user_func(function () {
                    return (extension_loaded('ZIP'));
                })
            ],
        ];

        $this->runCheckSystem();
    }

    public function runCheckSystem()
    {
        foreach ($this->MODULES as $key => $value) {
            $this->MODULES[$key]['pass'] = $this->MODULES[$key]['function'];

            if (!$this->MODULES[$key]['pass']) {
                $this->NOTICE[] = array(
                    'label' => $this->MODULES[$key]['label']
                );
            }

        }
    }

    public function list()
    {
        return $this->MODULES;
    }

    public function command()
    {
        $this->DATABASE['HOST'] = (isset($_POST['host'])) ? $_POST['host'] : '';
        $this->DATABASE['USER'] = (isset($_POST['username'])) ? $_POST['username'] : '';
        $this->DATABASE['NAME'] = (isset($_POST['dbname'])) ? $_POST['dbname'] : '';
        $this->DATABASE['PASSWORD'] = (isset($_POST['password'])) ? $_POST['password'] : '';

        $this->COMMAND = [
            'php ',
            $this->PATH . '/artisan initialize:send',
            ' --DB_HOST=',
            $this->DATABASE['HOST'],
            ' --DB_PASS=',
            $this->DATABASE['PASSWORD'],
            ' --DB_USER=',
            $this->DATABASE['USER'],
            ' --DB_NAME=',
            $this->DATABASE['NAME'],
            ' --STRIPE_ID=',
            isset($_POST['idstripe']) ? $_POST['idstripe'] : '',
            ' --STRIPE_SK=',
            isset($_POST['sk']) ? $_POST['sk'] : '',
            ' --STRIPE_PK=',
            isset($_POST['pk']) ? $_POST['pk'] : '',
            ' --STRIPE_SANDBOX_PK=',
            isset($_POST['pk_sambox']) ? $_POST['pk_sambox'] : '',
            ' --STRIPE_SANDBOX_SK=',
            isset($_POST['sk_sambox']) ? $_POST['sk_sambox'] : ''
        ];

        return implode('', $this->COMMAND);
    }

}

class HandleDataBase extends InstallPackage
{
    private $conn;

    public function connect()
    {
        $this->conn = mysqli_connect(parent::getConnect());
    }

    public function update($sql = '')
    {
        if ($this->conn->query($sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: ";
        }
    }

}


$class = new InstallPackage();

/**
 * Execute commando
 */
$exit_string = shell_exec($class->command());


/**
 * Get output command
 */

$install = strpos($exit_string, 'complete_success_system');

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
            <p class="lead">Antes de continuar debemos verificar que tu sistema cumple con los requerimientos básicos de
                instalación</p>
        </div>

        <div class="row">
            <div class="col-md-4 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Requerimientos</span>
                    <span class="badge badge-secondary badge-pill"><?php echo count($class->list()) ?></span>
                </h4>
                <ul class="list-group mb-3">
                    <?php foreach ($class->list() as $key => $module) { ?>
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                                <h6 class="my-0"><?php echo $key ?></h6>
                                <small class="text-muted"><?php echo $module['label'] ?></small>

                                <?php if ($module['pass']) {
                                    ?>
                                    <div class="mt-1">
                                        <span class="badge badge-success">Check</span>
                                    </div>
                                    <?php
                                } ?>


                                <?php if (!$module['pass']) {
                                    ?>
                                    <div class="mt-1">
                                        <span class="badge badge-danger">No check</span>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                            <span class="text-muted"> </span>
                        </li>
                    <?php } ?>

                </ul>

            </div>
            <div class="col-md-8 order-md-1">
                <h4 class="mb-3">Datos de Conexión</h4>
                <form name="formulario" method="POST" class="needs-validation" novalidate="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="host">Database host <span class="text-muted">127.0.0.1</span></label>
                            <input name="host"
                                   type="text" class="form-control"
                                   placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="database">Database name</label>
                            <input name="dbname" type="text" class="form-control"
                                   id="lastName" placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username">Username <span class="text-muted">root</span></label>
                            <input name="username" type="text" class="form-control"
                                   placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label name="password" for="password">Password <span class="text-muted">(Vacio si no tienes clave)</span></label>
                            <input type="password" class="form-control"
                                   placeholder="">
                        </div>
                    </div>

                    <hr class="mb-4">
                    <h4 class="mb-3">Datos de pago (STRIPE)</h4>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="host">Id stripe <span class="text-muted"></span></label>
                            <input name="idstripe" type="text" class="form-control"
                                   placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="database">Clave secreta <span class="text-muted">SK_xxxxxxx</span></label>
                            <input name="sk" type="text" class="form-control"
                                   id="lastName" placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="database">Clave publica <span class="text-muted">PK_xxxxxxx</span></label>
                            <input name="pk" type="text" class="form-control"
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
                            <input name="sk_sambox" type="text"
                                   class="form-control" placeholder="" value="" required="">
                            <div class="invalid-feedback">
                                Campos requeridos
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username">Clave publica <span class="text-muted">SAMBOX</span></label>
                            <input name="pk_sambox" type="text"
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
            <p class="mb-1">© <?php echo date("Y") ?> LeangaSoftware</p>
        </footer>
    </div>
    <?php
}
?>

<?php //?>
<!--<div class="container">-->
<!--    <div class="py-5 text-center">-->
<!--        <img class="d-block mx-auto mb-4"-->
<!--             src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72"-->
<!--             height="72">-->
<!--        <h2>INSTALACIÓN</h2>-->
<!--        <p class="lead">Antes de continuar debemos verificar que tu sistema cumple con los requerimientos básicos de-->
<!--            instalación</p>-->
<!--    </div>-->
<!---->
<!--    <div class="row">-->
<!--        <div class="col-md-12 order-md-1">-->
<!--            <h4 class="mb-3">Datos de Conexión</h4>-->
<!--            <form name="formulario" method="POST" class="needs-validation" novalidate="">-->
<!--                <div class="row">-->
<!--                    <div class="col-md-6 mb-3">-->
<!--                        <label for="host">Database host <span class="text-muted">127.0.0.1</span></label>-->
<!--                        <input name="host"-->
<!--                               type="text" class="form-control"-->
<!--                               placeholder="" value="" required="">-->
<!--                        <div class="invalid-feedback">-->
<!--                            Campos requeridos-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-md-6 mb-3">-->
<!--                        <label for="database">Database name</label>-->
<!--                        <input name="dbname" type="text" class="form-control"-->
<!--                               id="lastName" placeholder="" value="" required="">-->
<!--                        <div class="invalid-feedback">-->
<!--                            Campos requeridos-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="row">-->
<!--                    <div class="col-md-6 mb-3">-->
<!--                        <label for="username">Username <span class="text-muted">root</span></label>-->
<!--                        <input name="username" type="text" class="form-control"-->
<!--                               placeholder="" value="" required="">-->
<!--                        <div class="invalid-feedback">-->
<!--                            Campos requeridos-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-md-6 mb-3">-->
<!--                        <label name="password" for="password">Password <span class="text-muted">(Vacio si no tienes clave)</span></label>-->
<!--                        <input type="password" class="form-control"-->
<!--                               placeholder="">-->
<!--                    </div>-->
<!--                </div>-->
<!---->
<!--                <hr class="mb-4">-->
<!--                <h4 class="mb-3">Datos de pago (STRIPE)</h4>-->
<!--                <div class="row">-->
<!--                    <div class="col-md-12 mb-3">-->
<!--                        <label for="host">Id stripe <span class="text-muted"></span></label>-->
<!--                        <input name="idstripe" type="text" class="form-control"-->
<!--                               placeholder="" value="" required="">-->
<!--                        <div class="invalid-feedback">-->
<!--                            Campos requeridos-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-md-12 mb-3">-->
<!--                        <label for="database">Clave secreta <span class="text-muted">SK_xxxxxxx</span></label>-->
<!--                        <input name="sk" type="text" class="form-control"-->
<!--                               id="lastName" placeholder="" value="" required="">-->
<!--                        <div class="invalid-feedback">-->
<!--                            Campos requeridos-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-md-12 mb-3">-->
<!--                        <label for="database">Clave publica <span class="text-muted">PK_xxxxxxx</span></label>-->
<!--                        <input name="pk" type="text" class="form-control"-->
<!--                               id="lastName" placeholder="" value="" required="">-->
<!--                        <div class="invalid-feedback">-->
<!--                            Campos requeridos-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <hr class="mb-4">-->
<!--                <h4 class="mb-3">Datos de pago (STRIPE) Modo desarrollador</h4>-->
<!--                <div class="row">-->
<!--                    <div class="col-md-6 mb-3">-->
<!--                        <label for="username">Clave secreta <span class="text-muted">SAMBOX</span></label>-->
<!--                        <input name="sk_sambox" type="text"-->
<!--                               class="form-control" placeholder="" value="" required="">-->
<!--                        <div class="invalid-feedback">-->
<!--                            Campos requeridos-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-md-6 mb-3">-->
<!--                        <label for="username">Clave publica <span class="text-muted">SAMBOX</span></label>-->
<!--                        <input name="pk_sambox" type="text"-->
<!--                               class="form-control" placeholder="" value="" required="">-->
<!--                        <div class="invalid-feedback">-->
<!--                            Campos requeridos-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!---->
<!--                <hr class="mb-4">-->
<!--                <button class="btn btn-primary btn-lg btn-block" type="submit">Continiar con la instalación-->
<!--                </button>-->
<!--            </form>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <footer class="my-5 pt-5 text-muted text-center text-small">-->
<!--        <p class="mb-1">© --><?php //echo date("Y") ?><!-- LeangaSoftware</p>-->
<!--    </footer>-->
<!--</div>-->
<?php //?>
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
