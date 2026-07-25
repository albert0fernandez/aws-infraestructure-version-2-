<!--
    * Nombre del fichero: resource_room.php
    * Descripción: Página para listar recursos por aula.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada: 
    *   - room_id (opcional)
    *   - resource_id_del (opcional)
    *   - page (opcional)
    *
    * Salida: 
    *   - HTML con la lista de recursos por aula
-->


<div class="container is-fluid mb-6">
    <h1 class="title">Recursos</h1>
    <h2 class="subtitle">Lista de recursos por aula</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        require_once "./php/main.php";
    ?>
    <div class="columns">
        <div class="column is-one-third">
            <h2 class="title has-text-centered">Aulas</h2>
            <?php
                $aulas=conexion();
                $aulas=$aulas->query("SELECT * FROM t_aula");
                if($aulas->rowCount()>0){
                    $aulas=$aulas->fetchAll();
                    foreach($aulas as $row){
                        echo '<a href="index.php?vista=resource_room&room_id='.$row['aula_id'].'" class="button is-link is-inverted is-fullwidth">'.$row['aula_nombre'].'</a>';
                    }
                }else{
                    echo '<p class="has-text-centered" >No hay aulas registradas</p>';
                }
                $aulas=null;
            ?>
        </div>
        <div class="column">
            <?php
                $aula_id = (isset($_GET['room_id'])) ? $_GET['room_id'] : 0;

                // Verificando aula
                $check_aula=conexion();
                $check_aula=$check_aula->query("SELECT * FROM t_aula WHERE aula_id='$aula_id'");

                if($check_aula->rowCount()>0){

                    $check_aula=$check_aula->fetch();

                    echo '
                        <h2 class="title has-text-centered">'.$check_aula['aula_nombre'].'</h2>
                        <p class="has-text-centered pb-6" >'.$check_aula['aula_ubicacion'].'</p>
                    ';

                    require_once "./php/main.php";

                    // Eliminar recurso
                    if(isset($_GET['resource_id_del'])){
                        require_once "./php/recurso_eliminar.php";
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
                    $url="index.php?vista=resource_room&room_id=$aula_id&page="; // <==
                    $registros=15;
                    $busqueda="";

                    // Paginador recurso
                    require_once "./php/recurso_lista.php";

                }else{
                    echo '<h2 class="has-text-centered title" >Seleccione un aula para empezar</h2>';
                }
                $check_aula=null;
            ?>
        </div>
    </div>
</div>
