<?php
	/**
	 * Nombre del fichero: historial_uso_lista.php
	 * Descripción: Este script genera una lista del historial de uso de recursos.
	 *
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 * 
	 * Parámetros de entrada: 
	 *   - $pagina 
	 *   - $registros 
	 *   - $busqueda  
	 *   - $url
	 *
	 * Salida: 
	 *    - HTML con la tabla del historial de uso
	 */

	require_once "main.php";

	$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
	$tabla="";

	$campos="t_historial_uso.uso_id,t_historial_uso.usuario_id,t_historial_uso.recurso_id,t_historial_uso.uso_fecha_inicio, t_historial_uso.uso_fecha_fin, t_recurso.recurso_nombre, t_usuario.usuario_usuario";

	if(isset($busqueda) && $busqueda!=""){
		$consulta_datos="SELECT $campos FROM t_historial_uso 
			INNER JOIN t_usuario ON t_historial_uso.usuario_id=t_usuario.usuario_id 
				INNER JOIN t_recurso ON t_historial_uso.recurso_id=t_recurso.recurso_id 
					WHERE t_recurso.recurso_nombre LIKE '%$busqueda%' 
					OR t_usuario.usuario_nombre LIKE '%$busqueda%' 
						OR t_usuario.usuario_apellido LIKE '%$busqueda%' 
							OR t_usuario.usuario_usuario LIKE '%$busqueda%' 
					ORDER BY t_historial_uso.uso_fecha_inicio 
						ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(uso_id) FROM t_historial_uso 
		INNER JOIN t_usuario ON t_historial_uso.usuario_id=t_usuario.usuario_id 
			INNER JOIN t_recurso ON t_historial_uso.recurso_id=t_recurso.recurso_id 
				WHERE t_recurso.recurso_nombre LIKE '%$busqueda%' 
				OR t_usuario.usuario_nombre LIKE '%$busqueda%' 
					OR t_usuario.usuario_apellido LIKE '%$busqueda%' 
						OR t_usuario.usuario_usuario LIKE '%$busqueda%'"; 

	}else{

		$consulta_datos="SELECT $campos FROM t_historial_uso 
			INNER JOIN t_recurso ON t_historial_uso.recurso_id=t_recurso.recurso_id 
				INNER JOIN t_usuario ON t_historial_uso.usuario_id=t_usuario.usuario_id 
				ORDER BY t_historial_uso.uso_fecha_inicio 
					ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(uso_id) FROM t_historial_uso
			INNER JOIN t_recurso ON t_historial_uso.recurso_id=t_recurso.recurso_id 
				INNER JOIN t_usuario ON t_historial_uso.usuario_id=t_usuario.usuario_id";
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
						<th>Recurso</th>
						<th>Usuario</th>
						<th>Fecha inicio</th>
						<th>Fecha fin</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody>
		';

		if($total>=1 && $pagina<=$Npaginas){
			$contador=$inicio+1;
			$pag_inicio=$inicio+1;
			foreach($datos as $rows){
				$tabla.='
					<tr class="has-text-centered" >
						<td>'.$contador.'</td>
						<td>'.$rows['recurso_nombre'].'</td>
						<td>'.$rows['usuario_usuario'].'</td>
						<td>'.$rows['uso_fecha_inicio'].'</td>
						<td>'.$rows['uso_fecha_fin'].'</td>
						<td>
							<a href="index.php?vista=history_use_update&use_id_up='.$rows['uso_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
						</td>
					</tr>
				';
				$contador++;
			}
			$pag_final=$contador-1;
		}else{
			if($total>=1){
				$tabla.='
					<tr class="has-text-centered" >
						<td colspan="7">
							<a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
								Haga clic  para recargar el listado
							</a>
						</td>
					</tr>
				';
			}else{
				$tabla.='
					<tr class="has-text-centered" >
						<td colspan="7">
							No hay registros en el sistema
						</td>
					</tr>
				';
			}
		}

	$tabla.='</tbody></table>

	</div>';

	if($total>0 && $pagina<=$Npaginas){
		$tabla.='<p class="has-text-right">Mostrando registros de uso de recursos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
	}

	$conexion=null;
	echo $tabla;

	if($total>=1 && $pagina<=$Npaginas){
		echo paginador_tablas($pagina,$Npaginas,$url,7);
	}