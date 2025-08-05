<?php
require '../../includes/app.php';

use App\Vendedor;

estaAutenticado();

$vendedor = new Vendedor;

//mensajes de errores
$errores = Vendedor::getErrores();

//enviar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $vendedor  = new Vendedor($_POST['vendedor']);

    $errores = $vendedor->validar();

    if(empty($errores)) {

        $vendedor->guardar();
        
    }
}


incluirTemplate('header');
?>

  <main class="contenedor">
        <h1>Registrar Vendedor</h1>

        <?php  foreach($errores as $error):  ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach;  ?>

        <a href="/admin" class="boton boton-verde">Volver</a> 

        <form class="formulario" method="POST" action="/admin/vendedores/crear.php">
            <?php include '../../includes/templates/formulario_vendedores.php' ?>

            <input type="submit" value="Registrar Vendedor" class="boton boton-verde">
        </form>
    </main>
    

<?php incluirTemplate('footer');?>