<?php
	/**
	 * Nombre del fichero: mantenimiento_lista.php
	 * Descripción: Este script genera una lista del historial de uso de recursos.
	 *
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 * 
	 */

	require_once "main.php";

	$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
	$tabla="";

	$campos="t_mantenimiento.mantenimiento_id,t_mantenimiento.usuario_id,t_mantenimiento.recurso_id,t_mantenimiento.mantenimiento_fecha,t_mantenimiento.mantenimiento_descripcion, t_recurso.recurso_nombre, t_usuario.usuario_usuario";

	if(isset($busqueda) && $busqueda!=""){
		$consulta_datos="SELECT $campos FROM t_mantenimiento 
			INNER JOIN t_usuario ON t_mantenimiento.usuario_id=t_usuario.usuario_id 
				INNER JOIN t_recurso ON t_mantenimiento.recurso_id=t_recurso.recurso_id 
					WHERE t_recurso.recurso_nombre LIKE '%$busqueda%' 
					OR t_usuario.usuario_nombre LIKE '%$busqueda%' 
						OR t_usuario.usuario_apellido LIKE '%$busqueda%' 
							OR t_usuario.usuario_usuario LIKE '%$busqueda%' 
					ORDER BY t_mantenimiento.mantenimiento_fecha 
						ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(mantenimiento_id) FROM t_mantenimiento 
		INNER JOIN t_usuario ON t_mantenimiento.usuario_id=t_usuario.usuario_id 
			INNER JOIN t_recurso ON t_mantenimiento.recurso_id=t_recurso.recurso_id 
				WHERE t_recurso.recurso_nombre LIKE '%$busqueda%' 
				OR t_usuario.usuario_nombre LIKE '%$busqueda%' 
					OR t_usuario.usuario_apellido LIKE '%$busqueda%' 
						OR t_usuario.usuario_usuario LIKE '%$busqueda%'"; 

	}else{

		$consulta_datos="SELECT $campos FROM t_mantenimiento
			INNER JOIN t_recurso ON t_mantenimiento.recurso_id=t_recurso.recurso_id
				INNER JOIN t_usuario ON t_mantenimiento.usuario_id=t_usuario.usuario_id
				ORDER BY t_mantenimiento.mantenimiento_fecha
					ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(mantenimiento_id) FROM t_mantenimiento
			INNER JOIN t_recurso ON t_mantenimiento.recurso_id=t_recurso.recurso_id
				INNER JOIN t_usuario ON t_mantenimiento.usuario_id=t_usuario.usuario_id";
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
						<th>Fecha</th>
						<th>Descripción</th>
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
						<td>'.$rows['mantenimiento_fecha'].'</td>
						<td>'.$rows['mantenimiento_descripcion'].'</td>
						<td>
							<a href="index.php?vista=maintenance_update&maintenance_id_up='.$rows['mantenimiento_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
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
		$tabla.='<p class="has-text-right">Mostrando registros de mantenimiento de recursos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
	}

	$conexion=null;
	echo $tabla;
	
	// Se crea un nuevo documento XML

	$xml = new DOMDocument("1.0", "UTF-8");

	// Creación del elemento raíz
	$elementoRaiz = $xml->createElement("mantenimientos");
	$xml->appendChild($elementoRaiz);	

	foreach($datos as $row){ 
		$i=1;
		$elementoFila = $xml->createElement("mantenimiento");
		$elementoRaiz->appendChild($elementoFila);
		// Creación de los elementos para cada columna
		$elementoColumna =  $xml->createElement('mantenimiento_id', $row['mantenimiento_id']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('usuario_id', $row['usuario_id']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('recurso_id', $row['recurso_id']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('fecha', $row['mantenimiento_fecha']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('descripcion', $row['mantenimiento_descripcion']);
		$elementoFila->appendChild($elementoColumna);
	}

	$ruta= 'xml/consultas/mantenimientos/';

	// Comprobar si existe el directorio para guardar el resultado de la consulta y crearlo de no ser así
	crear_directorio($ruta);

	if ($total > 0) {
		$xml->formatOutput = true;
		$pi = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../../schemas/mantenimiento_estilo_transformacion.xslt"');
		$xml->insertBefore($pi, $xml->documentElement);
		$nombre_xml=nombrar_xml("cons-mantenimientos-id-").'.xml';
		$xml->save($ruta.$nombre_xml);

		echo '
			<div class="has-text-centered">
					<form method="post" action="index.php?vista=xml_display">
						<input type="hidden" name="xml_nombre" value="'.$nombre_xml.'"/><br>
						<button type="submit" class="button is-success is-rounded is-small" title="Visualizar '.$nombre_xml.'">
							Visualizar fichero guardado
						</button>
					</form>
					<br>
			<div>
		';
	}

	if($total>=1 && $pagina<=$Npaginas){
		echo paginador_tablas($pagina,$Npaginas,$url,7);
	}