<!--
    * Nombre del fichero: history_use_search.php
    * Descripción: Página para buscar y gestionar el historial de uso de recursos.
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    * Parámetros de entrada: 
    *    - POST['modulo_buscador']: módulo del buscador.
    *    - POST['txt_buscador']: texto de búsqueda.
    *    - POST['eliminar_buscador']: eliminar búsqueda.
    *    - GET['history_use_id_del']: ID del historial de uso a eliminar.
    *    - GET['page']: número de página para la paginación.
    * Parámetros de salida: 
    *    - Muestra la interfaz de búsqueda y los resultados del historial de uso.
-->

<div class="container is-fluid mb-6">
	<h1 class="title">Historial de uso de recursos</h1>
	<h2 class="subtitle">Buscar uso</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        require_once "./php/main.php"; // Cargar funciones principales

        if(isset($_POST['modulo_buscador'])){
            require_once "./php/buscador.php"; // Cargar módulo de búsqueda
        }

        if(!isset($_SESSION['busqueda_historial_uso']) && empty($_SESSION['busqueda_historial_uso'])){
    ?>
    <div class="columns">
        <div class="column">
            <form action="" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="historial_uso">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estás buscando?" title="Introduce el nombre o código de un recurso o el nombre, apellido, usuario o email de un usuario"
                            pattern="[A-Z]{3}-[0-9]{4}|[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,50}|[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}|[a-z0-9]{4,20}|[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}" maxlength="70" >
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
                <input type="hidden" name="modulo_buscador" value="historial_uso"> 
                <input type="hidden" name="eliminar_buscador" value="historial_uso">
                <p>Estas buscando <strong>"<?php echo $_SESSION['busqueda_historial_uso']; ?>"</strong></p>
                <br>
                <button type="submit" class="button is-danger is-rounded">Eliminar busqueda</button>
            </form>
        </div>
    </div>
    <?php
            // Eliminar recurso
            if(isset($_GET['history_use_id_del'])){
                require_once "./php/historial_uso_eliminar.php"; // Cargar script para eliminar historial de uso
            }

            // Paginación
            if(!isset($_GET['page'])){
                $pagina=1;
            }else{
                $pagina=(int) $_GET['page'];
                if($pagina<=1){
                    $pagina=1;
                }
            }

            $pagina=limpiar_cadena($pagina);
            $url="index.php?vista=history_use_search&page="; // URL para la paginación
            $registros=15;
            $busqueda=$_SESSION['busqueda_historial_uso']; // Texto de búsqueda

            // Paginador historial_uso
            require_once "./php/historial_uso_lista.php"; // Cargar lista de historial de uso
        } 
    ?>
</div>