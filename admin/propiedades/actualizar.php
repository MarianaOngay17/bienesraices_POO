<?php

use App\Propiedad;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as Image;

require '../../includes/app.php';

estaAutenticado();

//validar url por id valido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id){
    header('Location: /admin');
}

//base de datos
$db = conectarDB();

//consulta datos propiedad

$propiedad = Propiedad::find($id);

//consulta vendedores

$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//mensajes de errores

$errores = Propiedad::getErrores();

//enviar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //asignar atributos
    $args = $_POST['propiedad'];

    $propiedad->sincronizar($args);
    
    //validacion
    $errores = $propiedad->validar();

    //subida de archivos
    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

    if($_FILES['propiedad']['tmp_name']['imagen']){
        $manager = new Image(Driver::class);
        $imagen = $manager->read($_FILES['propiedad']['tmp_name']['imagen'])->cover(800, 600);
        $propiedad->setImagen($nombreImagen);
    }

    if(empty($errores)) {
        $imagen->save(CARPETA_IMAGENES . $nombreImagen);

        $propiedad->guardar();
    }

}

incluirTemplate('header');
?>
    <main class="contenedor">
        <h1>Actualizar propiedad</h1>

        <?php  foreach($errores as $error):  ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach;  ?>

        <a href="/admin" class="boton boton-verde">Volver</a> 

        <form class="formulario" method="POST" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_propiedades.php' ?>

            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>
    </main>
    
<?php incluirTemplate('footer');?>