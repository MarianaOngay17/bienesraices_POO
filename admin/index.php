<?php 
require '../includes/funciones.php';

$auth = estaAutenticado();

if(!$auth){
    header("Location: /");
}

//conexion
require '../includes/config/database.php';
$db = conectarDB();

//query

$query = "SELECT * FROM propiedades";

//consulta

$resultadoConsulta = mysqli_query($db, $query);

//mensaje condicional
$resultado = $_GET['resultado'] ?? null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if($id){

        //eliminar imagen
        $query = "SELECT imagen FROM propiedades WHERE id = $id ;";
        $resultado = mysqli_query($db, $query);
        $propiedad = mysqli_fetch_assoc($resultado);
        unlink('../imagenes/' . $propiedad['imagen']);

        //eliminar propiedad
        $query = "DELETE FROM propiedades WHERE id = $id ;";
        $resultado = mysqli_query($db, $query);

        if($resultado){
            header('Location: /admin?resultado=3');
        }
    }
}

//template
incluirTemplate('header');
?>
    <main class="contenedor">
        <h1>Administrador de Bienes Raíces</h1>
        <?php if($resultado == 1): ?>
            <p class="alerta exito">Anuncio Creado Correctamente</p>
        <?php elseif($resultado == 2): ?>
            <p class="alerta exito">Anuncio Actualizado Correctamente</p>
        <?php elseif($resultado == 3): ?>
            <p class="alerta exito">Anuncio Eliminado Correctamente</p>
        <?php endif;?>

        <a href="/admin/propiedades/crear.php" class="boton boton-verde">Nueva Propiedad</a>
    
        <table class="propiedades">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Imagen</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody> <!--mostrar resultados-->
            <?php while($propiedad = mysqli_fetch_assoc($resultadoConsulta)):   ?>    
                <tr>
                    <td> <?php echo $propiedad['id']; ?> </td>
                    <td> <?php echo $propiedad['titulo']; ?> </td>
                    <td> <img src="imagenes/<?php echo $propiedad['imagen']; ?>" class="imagen-tabla"> </td>
                    <td>  $ <?php echo $propiedad['precio']; ?> </td>
                    <td>
                        <form method="POST" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                        <a href="admin/propiedades/actualizar.php?id=<?php echo $propiedad['id']; ?>" class="boton-amarillo-block">Actualizar</a>
                    </td>
                </tr>
            <?php  endwhile;?>
            </tbody>
        </table>

    
    </main>
    
<?php 
mysqli_close($db); //cerrar conexion
incluirTemplate('footer');
?>