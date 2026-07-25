<?php
	/**
	 * Nombre del fichero: proveedor_lista.php
	 * Descripción: Este script genera una lista de proveedores con opciones para actualizar y eliminar.
	 *
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 *
	 * Parámetros de entrada: 
	 *    - $pagina
	 *    - $registros 
	 *    - $busqueda 
	 *    - $url
	 *
	 *  Parámetros de salida: HTML con la tabla de usuarios y paginación
	 */
	
	$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
	$tabla="";

	if(isset($busqueda) && $busqueda!=""){

		$consulta_datos="SELECT * FROM t_proveedor WHERE ((proveedor_id!='".$_SESSION['id']."') AND (proveedor_nombre LIKE '%$busqueda%' OR proveedor_email LIKE '%$busqueda%')) ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(proveedor_id) FROM t_proveedor WHERE ((proveedor_id!='".$_SESSION['id']."') AND (proveedor_nombre LIKE '%$busqueda%' OR proveedor_email LIKE '%$busqueda%'))";

	}else{

		$consulta_datos="SELECT * FROM t_proveedor WHERE proveedor_id!='".$_SESSION['id']."' ORDER BY proveedor_nombre ASC LIMIT $inicio,$registros";

		$consulta_total="SELECT COUNT(proveedor_id) FROM t_proveedor WHERE proveedor_id!='".$_SESSION['id']."'";
		
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
					<th>Teléfono</th>
					<th>Email</th>
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
					<td>'.$rows['proveedor_nombre'].'</td>
					<td>'.$rows['proveedor_telefono'].'</td>
					<td>'.$rows['proveedor_email'].'</td>
					<td>
						<a href="index.php?vista=supplier_update&supplier_id_up='.$rows['proveedor_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
					</td>
					<td>
						<a href="'.$url.$pagina.'&supplier_id_del='.$rows['proveedor_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
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
	$elementoRaiz = $xml->createElement("proveedores");
	$xml->appendChild($elementoRaiz);	

	foreach($datos as $row){ 
		$i=1;
		$elementoFila = $xml->createElement("proveedor");
		$elementoRaiz->appendChild($elementoFila);
		// Creación de los elementos para cada columna
		$elementoColumna =  $xml->createElement('proveedor_id', $row['proveedor_id']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('proveedor_nombre', $row['proveedor_nombre']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('proveedor_telefono', $row['proveedor_telefono']);
		$elementoFila->appendChild($elementoColumna);
		$elementoColumna =  $xml->createElement('proveedor_email', $row['proveedor_email']);
		$elementoFila->appendChild($elementoColumna);
	}

	$ruta= 'xml/consultas/proveedores/';

	// Comprobar si existe el directorio para guardar el resultado de la consulta y crearlo de no ser así
	crear_directorio($ruta);

	if ($total > 0) {
		$xml->formatOutput = true;
		$pi = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../../schemas/proveedor_estilo_transformacion.xslt"');
		$xml->insertBefore($pi, $xml->documentElement);
		$nombre_xml=nombrar_xml("cons-proveedores-id-").'.xml';
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