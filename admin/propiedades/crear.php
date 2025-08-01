<?php 
require '../../includes/app.php';

use App\Propiedad;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as Image;

estaAutenticado();

//base de datos
$db = conectarDB();

$propiedad = new Propiedad;

//consulta vendedores

$consulta = "SELECT * FROM vendedores";

$resultado = mysqli_query($db, $consulta);

//mensajes de errores

$errores = Propiedad::getErrores();

//enviar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $propiedad = new Propiedad($_POST['propiedad']);
    
    //generar nombre imagen
    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
    if($_FILES['propiedad']['tmp_name']['imagen']){
        $manager = new Image(Driver::class);
        $imagen = $manager->read($_FILES['propiedad']['tmp_name']['imagen'])->cover(800, 600);
        $propiedad->setImagen($nombreImagen);
    }

    $errores = $propiedad->validar();
   
     if(empty($errores)) {
        /*subida de archivos*/

        if(!is_dir(CARPETA_IMAGENES)){
            mkdir(CARPETA_IMAGENES);
        }

        //guardar imagen en servidor

        $imagen->save(CARPETA_IMAGENES . $nombreImagen);

        $resultado = $propiedad->guardar();
        if($resultado){
            header("Location: /admin?resultado=1");
        }
    }

}

incluirTemplate('header');
?>
    <main class="contenedor">
        <h1>Crear</h1>

        <?php  foreach($errores as $error):  ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach;  ?>

        <a href="/admin" class="boton boton-verde">Volver</a> 

        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_propiedades.php' ?>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>
    
<?php incluirTemplate('footer');?>