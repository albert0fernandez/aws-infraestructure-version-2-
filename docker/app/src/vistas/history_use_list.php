<!--
    * Nombre del fichero: history_use_list.php
    * Descripción: Muestra el historial de uso de recursos.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada: 
    *    - use_id_del (opcional): ID del uso a eliminar.
    *    - page (opcional): Número de página para la paginación.
    *    - room_id (opcional): ID del aula para filtrar el historial.
    *
    * Salida: Ninguno
-->

<div class="container is-fluid mb-6">
    <h1 class="title">Historial de uso de recursos</h1>
    <h2 class="subtitle">Listado</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        require_once "./php/main.php";

        // Eliminar recurso
        if(isset($_GET['use_id_del'])){
            require_once "./php/historial_uso_eliminar.php";
        }

        if(!isset($_GET['page'])){
            $pagina=1;
        }else{
            $pagina=(int) $_GET['page'];
            if($pagina<=1){
                $pagina=1;
            }
        }

        $aula_id = (isset($_GET['room_id'])) ? $_GET['room_id'] : 0;

        $pagina=limpiar_cadena($pagina);
        $url="index.php?vista=resource_list&page="; // URL base para la paginación
        $registros=15;
        $busqueda="";

        // Paginador recurso
        require_once "./php/historial_uso_lista.php";
    ?>
</div>
