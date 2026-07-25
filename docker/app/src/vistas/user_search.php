<!--
    * Nombre del fichero: user_search.php
    * Descripción: Página para buscar usuarios en el inventario.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada: 
    *   - modulo_buscador (POST)
    *   - busqueda_usuario (SESSION)
    *   - user_id_del (GET) 
    *   - page (GET)
    * Salida: Ninguno
-->


<div class="container is-fluid mb-6">
    <h1 class="title">Usuarios</h1>
    <h2 class="subtitle">Buscar usuario</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        require_once "./php/main.php";

        // Verificar si se ha enviado el formulario de búsqueda
        if(isset($_POST['modulo_buscador'])){
            require_once "./php/buscador.php";
        }

        // Verificar si no hay una búsqueda activa
        if(!isset($_SESSION['busqueda_usuario']) && empty($_SESSION['busqueda_usuario'])){
    ?>
    <div class="columns">
        <div class="column">
            <form action="" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="usuario">   
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estás buscando?" title="Introduce el nombre, apellido, usuario o email que estás buscando)" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}|[a-z0-9]{4,20}|[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}" maxlength="70" >
                    </p>
                    <p class="control">
                        <button class="button is-info is-rounded" type="submit" >Buscar</button>
                    </p>
                </div>
            </form>
        </div>
    </div>
    <?php }else{ ?>
    <div class="columns">
        <div class="column">
            <form class="has-text-centered mt-6 mb-6" action="" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="usuario"> 
                <input type="hidden" name="eliminar_buscador" value="usuario">
                <p>Estas buscando <strong>"<?php echo $_SESSION['busqueda_usuario']; ?>"</strong></p>
                <br>
                <button type="submit" class="button is-danger is-rounded">Eliminar busqueda</button>
            </form>
        </div>
    </div>
    <?php
            // Eliminar usuario
            if(isset($_GET['user_id_del'])){
                require_once "./php/usuario_eliminar.php";
            }

            // Verificar la página actual
            if(!isset($_GET['page'])){
                $pagina=1;
            }else{
                $pagina=(int) $_GET['page'];
                if($pagina<=1){
                    $pagina=1;
                }
            }

            $pagina=limpiar_cadena($pagina);
            $url="index.php?vista=user_search&page="; // URL base para el paginador
            $registros=15;
            $busqueda=$_SESSION['busqueda_usuario']; // Término de búsqueda

            // Paginador usuario
            require_once "./php/usuario_lista.php";
        } 
    ?>
</div>