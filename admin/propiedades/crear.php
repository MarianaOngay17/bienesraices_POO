<?php 
require '../../includes/funciones.php';

$auth = estaAutenticado();

if(!$auth){
    header("Location: /");
}
//base de datos
require '../../includes/config/database.php';
$db = conectarDB();

//consulta vendedores

$consulta = "SELECT * FROM vendedores";

$resultado = mysqli_query($db, $consulta);

//mensajes de errores

$errores = [];

$titulo = '';
$precio = '';
$descripcion = '';
$habitaciones = '';
$wc = '';
$estacionamiento = '';
$vendedores_id = '';

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

    if(!$imagen['name'] || $imagen['error'] ) {
        $errores[] = 'La Imagen es Obligatoria';
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

        //generar nombre imagen

        $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
       
        //subir imagen
        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );

        $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, 
                habitaciones, wc, estacionamiento, creado,vendedores_id) 
                VALUES ( '$titulo', '$precio', '$nombreImagen', '$descripcion', 
                '$habitaciones', '$wc', '$estacionamiento',
                '$creado', '$vendedores_id');";

        $resultado = mysqli_query($db, $query);       
        
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
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título propiedad" value="<?php echo $titulo ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio propiedad" <?php echo $precio ?>>

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">
                
                <label for="descripcion">Descripcion:</label>
                <textarea id="descripcion" name="descripcion"><?php echo $descripcion ?></textarea>
            </fieldset>

            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="habitaciones">Habitaciones:</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9" <?php echo $habitaciones ?>>
                
                <label for="wc">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9" <?php echo $wc ?>>
                
                <label for="estacionamiento">Estacionamiento:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" <?php echo $estacionamiento ?>>

            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <select name="vendedor">
                    <option value="">-- Seleccione -- </option>
                    <?php while($vendedor = mysqli_fetch_assoc($resultado)): ?>
                        <option 
                            <?php echo $vendedores_id === $vendedor['id'] ? 'selected' : '';?> 
                            value="<?php echo $vendedor['id']?>" > 
                                <?php echo $vendedor['nombre'] . " " . $vendedor['apellido'];?>
                        </option>
                    <?php endwhile;?>
                </select>
            </fieldset>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>
    
<?php incluirTemplate('footer');?>