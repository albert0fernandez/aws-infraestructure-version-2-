<?php
/**
 * Nombre del fichero: departamento_lista.php
 * Descripción: Muestra una lista de departamentos con opciones para actualizar y eliminar.
 */

$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
$tabla="";

// Consultas modificadas para la tabla t_departamento
if(isset($busqueda) && $busqueda!=""){
    $consulta_datos="SELECT * FROM t_departamento WHERE 
                    (departmento_nombre LIKE '%$busqueda%' OR 
                     departmento_ubicacion LIKE '%$busqueda%') 
                    ORDER BY departmento_nombre ASC LIMIT $inicio,$registros";

    $consulta_total="SELECT COUNT(departmento_id) FROM t_departamento WHERE 
                    (departmento_nombre LIKE '%$busqueda%' OR 
                     departmento_ubicacion LIKE '%$busqueda%')";
}else{
    $consulta_datos="SELECT * FROM t_departamento ORDER BY departmento_nombre ASC LIMIT $inicio,$registros";
    $consulta_total="SELECT COUNT(departmento_id) FROM t_departamento";
}

$conexion=conexion();
$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

$total = $conexion->query($consulta_total);
$total = (int) $total->fetchColumn();

$Npaginas =ceil($total/$registros);

$tabla.='
<div class="table-container">
    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
        <thead>
            <tr class="has-text-centered">
                <th>#</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Responsable ID</th>
                <th colspan="2">Opciones</th>
            </tr>
        </thead>
        <tbody>
';

if($total>=1 && $pagina<=$Npaginas){
    $contador=$inicio+1;
    $pag_inicio=$inicio+1;
    foreach($datos as $rows){
        $tabla.='
            <tr class="has-text-centered">
                <td>'.$contador.'</td>
                <td>'.$rows['departmento_nombre'].'</td>
                <td>'.$rows['departmento_ubicacion'].'</td>
                <td>'.($rows['responsable_id'] ?? 'No asignado').'</td>
                <td>
                    <a href="index.php?vista=departamento_update&departamento_id_up='.$rows['departmento_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
                </td>
                <td>
                    <a href="'.$url.$pagina.'&departamento_id_del='.$rows['departmento_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
                </td>
            </tr>
        ';
        $contador++;
    }
    $pag_final=$contador-1;
}else{
    if($total>=1){
        $tabla.='
            <tr class="has-text-centered">
                <td colspan="6">
                    <a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
                        Haga clic aquí para recargar el listado
                    </a>
                </td>
            </tr>
        ';
    }else{
        $tabla.='
            <tr class="has-text-centered">
                <td colspan="6">
                    No hay departamentos registrados
                </td>
            </tr>
        ';
    }
}

$tabla.='</tbody></table></div>';

if($total>0 && $pagina<=$Npaginas){
    $tabla.='<p class="has-text-right">Mostrando departamentos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
}

$conexion=null;
echo $tabla;

if($total>=1 && $pagina<=$Npaginas){
    echo paginador_tablas($pagina,$Npaginas,$url,7);
}
?>
