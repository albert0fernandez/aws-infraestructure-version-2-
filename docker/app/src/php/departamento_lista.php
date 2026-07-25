<?php
	/**
	 * Nombre del fichero: departamento_lista.php
	 * Descripción: Este script genera una lista de departamentos con opciones para actualizar y eliminar.
	 *
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 *
	 */
	
	$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
	$tabla="";
	

	if(isset($busqueda) && $busqueda!=""){

		$consulta_datos="SELECT departamento_id, departamento_nombre, departamento_ubicacion, responsable_id, usuario_nombre, usuario_apellido FROM t_departamento JOIN t_usuario ON t_departamento.responsable_id = t_usuario.usuario_id WHERE (departamento_nombre LIKE '%$busqueda%' OR departamento_ubicacion LIKE '%$busqueda%' OR responsable_id LIKE '%$busqueda%') ORDER BY departamento_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(departamento_id) FROM t_departamento WHERE departamento_nombre LIKE '%$busqueda%' OR departamento_ubicacion LIKE '%$busqueda%' OR responsable_id LIKE '%$busqueda%'";

	}else{
		$consulta_datos="SELECT departamento_id, departamento_nombre, departamento_ubicacion, responsable_id, usuario_nombre, usuario_apellido FROM t_departamento JOIN t_usuario ON t_departamento.responsable_id = t_usuario.usuario_id WHERE departamento_id!='".$_SESSION['id']."' ORDER BY departamento_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(departamento_id) FROM t_departamento WHERE departamento_id!='".$_SESSION['id']."'";
		
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
					<th>Responsable</th>
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
				<tr class="has-text-centered" >
					<td>'.$contador.'</td>
					<td>'.$rows['departamento_nombre'].'</td>
					<td>'.$rows['departamento_ubicacion'].'</td>
					<td>'.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'</td>
					<td>
						<a href="index.php?vista=department_update&department_id_up='.$rows['departamento_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
					</td>
					<td>
						<a href="'.$url.$pagina.'&department_id_del='.$rows['departamento_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
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
							Haga clic aquí para recargar el listado
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

	$tabla.='</tbody></table></div>';

	if($total>0 && $pagina<=$Npaginas){
		$tabla.='<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
	}

	$conexion=null;
	echo $tabla;

		// Se crea un nuevo documento XML

	$xml = new DOMDocument("1.0", "UTF-8");

	// Creación del elemento raíz
	$elementoRaiz = $xml->createElement("departamentos");
	$xml->appendChild($elementoRaiz);	

	foreach($datos as $row){ 
		$i=1;
		$elementoFila = $xml->createElement("departamento");
		$elementoRaiz->appendChild($elementoFila);
		// Creación de los elementos para cada columna
		$elementoColumna =  $xml->createElement('departamento_id', $row['departamento_id']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('departamento_nombre', $row['departamento_nombre']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('departamento_ubicacion', $row['departamento_ubicacion']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('responsable_id', $row['responsable_id']);
		$elementoFila->appendChild($elementoColumna);
	}

	$ruta= 'xml/consultas/departamentos/';

	// Comprobar si existe el directorio para guardar el resultado de la consulta y crearlo de no ser así
	crear_directorio($ruta);

	if ($total > 0) {
		$xml->formatOutput = true;
		$pi = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../../schemas/departamento_estilo_transformacion.xslt"');
		$xml->insertBefore($pi, $xml->documentElement);
		$nombre_xml=nombrar_xml("cons-departamentos-id-").'.xml';
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