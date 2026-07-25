<?php 
    /**
    * Nombre del fichero: index.php
    * Descripción: Archivo principal que maneja las vistas y la sesión del usuario.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *    
    * Parámetros de entrada: 
    *   - $_GET['vista']
    *
    * Salida: HTML
    */ 

require "./inc/session_start.php"; 
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php include "./inc/head.php"; ?>
    </head>
    <body>
        <main>
            <?php 
                ob_start();

                if(!isset($_GET['vista']) || $_GET['vista']==""){
                    $_GET['vista']="login";
                }

                if(is_file("./vistas/".$_GET['vista'].".php") && $_GET['vista']!="login" && $_GET['vista']!="404"){

                    // Cerrar sesion
                    if((!isset($_SESSION['id']) || $_SESSION['id']=="") || (!isset($_SESSION['usuario']) || $_SESSION['usuario']=="")){
                        include "./vistas/logout.php";
                        exit();
                    }

                    include "./inc/navbar.php";

                    include "./vistas/".$_GET['vista'].".php";

                    include "./inc/script.php";

                }else{
                    if($_GET['vista']=="login"){
                        include "./vistas/login.php";
                    }else{
                        include "./vistas/404.php";
                    }
                }
            ob_flush() 
            ?>
        </main>
        <?php
            include "./inc/footer.php";
        ?>
    </body>
</html>