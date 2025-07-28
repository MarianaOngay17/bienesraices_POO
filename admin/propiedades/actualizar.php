<?php 
require '../../includes/funciones.php';

$auth = estaAutenticado();

if(!$auth){
    header("Location: /");
}

//validar url por id valido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id){
    header('Location: /admin');
}

//base de datos
require '../../includes/config/database.php';
$db = conectarDB();

//consulta datos propiedad

$consultaPropiedad = "SELECT * FROM propiedades WHERE id=$id ";
$resultadoPropiedad = mysqli_query($db, $consultaPropiedad);
$propiedad = mysqli_fetch_assoc($resultadoPropiedad);

//consulta vendedores

$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//mensajes de errores

$errores = [];

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedores_id = $propiedad['vendedores_id'];
$imagenPropiedad = $propiedad['imagen'];

//enviar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion =  mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db, $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedores_id = mysqli_real_escape_string($db, $_POST['vendedor']);
    $creado = date('Y/m/d');

    //asignar files a una variable
    $imagen = $_FILES['imagen'];


    if(!$titulo){
        $errores[] = "Debes añadir un título";
    }

    if(!$precio){
        $errores[] = "El Precio es obligatorio";
    }

    if( strlen($descripcion) < 50 ){
        $errores[] = "La descripción es obligatorio y debe tener al menos 50 caracteres";
    }

    if(!$habitaciones){
        $errores[] = "El Número de habitaciones es obligatorio";
    }

    if(!$wc){
        $errores[] = "El Número de Baños es obligatorio";
    }

    if(!$estacionamiento){
        $errores[] = "El Número de lugares de Estacionamientos es obligatorio";
    }

    if(!$vendedores_id){
        $errores[] = "Elige un vendedor";
    }

    $medida = 1000 * 1000;
    if($imagen['size'] > $medida){
        $errores[] = 'La Imagen es muy pesada';
    }

    //validar errores

     if(empty($errores)) {

        /*subida de archivos*/

        //crear carpeta
        $carpetaImagenes = '../../imagenes/';

        if(!is_dir($carpetaImagenes)){
             mkdir($carpetaImagenes);
        }

        $nombreImagen = '';

        if($imagen['name']){
            //eliminar imagen previa

            unlink($carpetaImagenes . $propiedad['imagen']);

            // //generar nombre imagen
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
       
            // //subir imagen
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );

        }else{
            $nombreImagen = $propiedad['imagen'];
        }

        $query = "UPDATE propiedades SET titulo = '$titulo',
                 precio = '$precio', imagen = '$nombreImagen',
                 descripcion = '$descripcion',
                 habitaciones = $habitaciones, wc = $wc,
                 estacionamiento = $estacionamiento,
                 vendedores_id = $vendedores_id WHERE id = $id;";

        $resultado = mysqli_query($db, $query);   
           
        if($resultado){
            header("Location: /admin?resultado=2");
        }
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
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título propiedad" value="<?php echo $titulo ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">
                
                <img src="/imagenes/<?php echo $imagenPropiedad?>" class="imagen-small">

                <label for="descripcion">Descripcion:</label>
                <textarea id="descripcion" name="descripcion"><?php echo $descripcion ?></textarea>
            </fieldset>

            <fieldset>
                <legend>Información Propiedad</legend>

                  <label for="habitaciones">Habitaciones:</label>
                <input 
                    type="number" 
                    id="habitaciones" 
                    name="habitaciones" 
                    placeholder="Ej: 3" 
                    min="1" 
                    max="9" 
                    value="<?php echo $habitaciones; ?>">
                
                <label for="wc">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9" value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento; ?>">

            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <select name="vendedor">
                    <option value="">-- Seleccione -- </option>
                    <?php while($vendedor = mysqli_fetch_assoc($resultado)): ?>
                        <option 
                            <?php echo $vendedores_id === $vendedor['id'] ? 'selected' : $propiedad['titulo'];?> 
                            value="<?php echo $vendedor['id']?>" > 
                                <?php echo $vendedor['nombre'] . " " . $vendedor['apellido'];?>
                        </option>
                    <?php endwhile;?>
                </select>
            </fieldset>

            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>
    </main>
    
<?php incluirTemplate('footer');?>