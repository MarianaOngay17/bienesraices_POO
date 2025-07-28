<?php 
require 'includes/funciones.php';
incluirTemplate('header');
?>
    <main class="contenedor">
        <h1>Casas y Depas en Venta</h1>

        <?php 
            $limite = 10;
            include 'includes/templates/anuncios.php' 
        ?>
            
        </div> <!--.contenedor-anuncios-->
    </main>

<?php incluirTemplate('footer');?>