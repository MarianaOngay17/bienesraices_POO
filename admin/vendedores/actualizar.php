<?php
require '../../includes/app.php';
use App\Vendedor;
estaAutenticado();

$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id){
    header('Location: /admin');
}

$vendedor = Vendedor::find($id);

//mensajes de errores
$errores = Vendedor::getErrores();

//enviar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //asignar atributos
    $args = $_POST['vendedor'];
    $vendedor->sincronizar($args);

    //validacion
    $errores = $vendedor->validar();

    //debuguear($errores);

    if(empty($errores)) {
        $vendedor->guardar();
    }

}


incluirTemplate('header');
?>

  <main class="contenedor">
        <h1>Actualizar Vendedor</h1>

        <?php  foreach($errores as $error):  ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach;  ?>

        <a href="/admin" class="boton boton-verde">Volver</a> 

        <form class="formulario" method="POST">
            <?php include '../../includes/templates/formulario_vendedores.php' ?>

            <input type="submit" value="Actualizar Vendedor" class="boton boton-verde">
        </form>
    </main>
    

<?php incluirTemplate('footer');?>