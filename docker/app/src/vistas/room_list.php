<!--
    * Nombre del fichero: room_list.php
    * Descripción: Lista de aulas con opciones para eliminar y paginar.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada: 
    *   - $_GET['room_id_del']
    *   - $_GET['page']
    *
    * Salida: HTML
-->


<div class="container is-fluid mb-6">
    <h1 class="title">Aulas</h1>
    <h2 class="subtitle">Lista de aula</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        require_once "./php/main.php";

        // Eliminar aula
        if(isset($_GET['room_id_del'])){
            require_once "./php/aula_eliminar.php";
        }

        if(!isset($_GET['page'])){
            $pagina=1;
        }else{
            $pagina=(int) $_GET['page'];
            if($pagina<=1){
                $pagina=1;
            }
        }

        $pagina=limpiar_cadena($pagina);
        $url="index.php?vista=room_list&page=";
        $registros=15;
        $busqueda="";

        // Paginador aula
        require_once "./php/aula_lista.php";
    ?>
</div>