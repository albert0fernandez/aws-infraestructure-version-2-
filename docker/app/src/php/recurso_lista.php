<?php
/**
 * Nombre del fichero: recurso_lista.php
 * Descripción: Genera una lista de recursos con opciones para visualizar, actualizar y eliminar.
 * 
 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
 * Fecha: Marzo 2025
 * 
 * Parámetros de entrada: 
 *   - pagina
 *   - registros
 *   - busqueda
 *   - aula_id
 * 
 * Salida: 
 *   - Tabla HTML con la información sobre los recursos.
 *   - Documento XML que contiene los datos de la tabla.
 */

require_once "main.php";

$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
$tabla="";

$campos="t_recurso.recurso_id,t_recurso.recurso_codigo, t_recurso.recurso_descripcion,t_recurso.estado_id,t_recurso.recurso_nombre,t_recurso.recurso_precio,t_recurso.aula_id,t_aula.aula_id, t_recurso.recurso_fecha_compra, t_recurso.recurso_fin_garantia, t_recurso.recurso_foto, t_aula.aula_nombre,t_estado.estado_descripcion";

if(isset($busqueda) && $busqueda!=""){
	$consulta_datos="SELECT $campos FROM t_recurso INNER JOIN t_aula ON t_recurso.aula_id=t_aula.aula_id INNER JOIN t_estado ON t_recurso.estado_id=t_estado.estado_id WHERE t_recurso.recurso_codigo LIKE '%$busqueda%' OR t_recurso.recurso_nombre LIKE '%$busqueda%' ORDER BY t_recurso.recurso_nombre ASC LIMIT $inicio,$registros";

	$consulta_total="SELECT COUNT(recurso_id) FROM t_recurso WHERE recurso_codigo LIKE '%$busqueda%' OR recurso_nombre LIKE '%$busqueda%'";

}elseif($aula_id>0){

	$consulta_datos="SELECT $campos FROM t_recurso INNER JOIN t_aula ON t_recurso.aula_id=t_aula.aula_id INNER JOIN t_estado ON t_recurso.estado_id=t_estado.estado_id WHERE t_recurso.aula_id='$aula_id' ORDER BY t_recurso.recurso_nombre ASC LIMIT $inicio,$registros";

	$consulta_total="SELECT COUNT(recurso_id) FROM t_recurso WHERE aula_id='$aula_id'";

}else{

	$consulta_datos="SELECT $campos FROM t_recurso INNER JOIN t_aula ON t_recurso.aula_id=t_aula.aula_id INNER JOIN t_estado ON t_recurso.estado_id=t_estado.estado_id ORDER BY t_recurso.recurso_nombre ASC LIMIT $inicio,$registros";

	$consulta_total="SELECT COUNT(recurso_id) FROM t_recurso";

}

$conexion=conexion();

$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

$total = $conexion->query($consulta_total);
$total = (int) $total->fetchColumn();

$Npaginas =ceil($total/$registros);

if($total>=1 && $pagina<=$Npaginas){
	$contador=$inicio+1;
	$pag_inicio=$inicio+1;
	foreach($datos as $rows){
		$tabla.='
			<article class="media">
				<figure class="media-left">
					<p class="image is-64x64">';
					if(is_file("./img/recurso/".$rows['recurso_foto'])){
						$tabla.='<img src="./img/recurso/'.$rows['recurso_foto'].'">';
						$camino="./img/recurso/".$rows['recurso_foto'];
					}else{
						$tabla.='<img src="./img/recurso.png">';
					}
					$tabla.='</p>
				</figure>
				
				<div class="media-content">
					<div class="content">
						<p>
						<strong>'.$contador.' - '.$rows['recurso_nombre'].'</strong><br>
						<strong>CODIGO:</strong> '.$rows['recurso_codigo'].', <strong>PRECIO:</strong> $'.$rows['recurso_precio'].', <strong>ESTADO:</strong> '.$rows['estado_descripcion'].', <strong>UBICACIÓN:</strong> '.$rows['aula_nombre'].'
						</p>
					</div>
					<div class="has-text-right">
						<a href="index.php?vista=resource_img&resource_id_up='.$rows['recurso_id'].'" class="button is-link is-rounded is-small">Imagen</a>
						<a href="index.php?vista=resource_update&resource_id_up='.$rows['recurso_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
						<a href="'.$url.$pagina.'&resource_id_del='.$rows['recurso_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
					</div>
				</div>
			</article>

			<hr>
		';
		$contador++;
	}
	$pag_final=$contador-1;
}else{
	if($total>=1){
		$tabla.='
			<p class="has-text-centered" >
				<a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
					Haga clic aquí para recargar el listado
				</a>
			</p>
		';
	}else{
		$tabla.='
			<p class="has-text-centered" >No hay registros en el sistema</p>
		';
	}
}

if($total>0 && $pagina<=$Npaginas){
	$tabla.='<p class="has-text-right">Mostrando recursos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
}

$conexion=null;
echo $tabla;

$xml = new DOMDocument("1.0", "UTF-8");

// Creación del elemento raíz
$elementoRaiz = $xml->createElement("recursos");
$xml->appendChild($elementoRaiz);	

foreach($datos as $row){ 
	$i=1;
	$elementoFila = $xml->createElement("recurso");
	$elementoRaiz->appendChild($elementoFila);		
	// Creación de los elementos para cada columna
	$elementoColumna =  $xml->createElement('recurso_id', $row['recurso_id']);
	$elementoFila->appendChild($elementoColumna);
	$elementoColumna =  $xml->createElement('recurso_nombre', $row['recurso_nombre']);
	$elementoFila->appendChild($elementoColumna);
	$elementoColumna =  $xml->createElement('recurso_precio', $row['recurso_precio']);
	$elementoFila->appendChild($elementoColumna);
	$elementoColumna =  $xml->createElement('recurso_estado', $row['estado_descripcion']);
	$elementoFila->appendChild($elementoColumna);
	$elementoColumna =  $xml->createElement('recurso_ubicacion', $row['aula_nombre']);
	$elementoFila->appendChild($elementoColumna);
}

$ruta= 'xml/consultas/';

// Comprobar si existe el directorio para guardar el resultado de la consulta y crearlo de no ser así
crear_directorio($ruta);

if ($total > 0) {
	$xml->formatOutput = true;
	$pi = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="../../schemas/estilo_transformacion.xslt"');
	$xml->insertBefore($pi, $xml->documentElement);
	$nombre_xml=nombrar_xml("cons-id-").'.xml';
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

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Masiva de Aulas</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .header, .footer {
            background-color: #3273dc;
            padding: 1rem;
            color: #fff;
            text-align: center;
        }
        .header h1, .footer p {
            margin: 0;
        }
        .form-title {
            color: #3273dc;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .button.is-primary {
            background-color: #3273dc;
            border-color: #3273dc;
        }
        .button.is-primary:hover {
            background-color: #275ab5;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Carga Masiva</h1>
    </div>

    <section class="section">
        <div class="container">
            <h2 class="form-title">Carga Masiva</h2>
            <form action="aula_carga_masiva.php" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label class="label">Seleccionar archivo</label>
                    <div class="control has-icons-left">
                        <input class="input" type="file" name="csv_file" accept=".csv" required>
                        <span class="icon is-small is-left">
                            <i class="fas fa-file-csv"></i>
                        </span>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-primary is-fullwidth" type="submit">Cargar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div class="footer">
        <p>&copy; 2025 RetaCantabria - ASIR1 - IES Alisal</p>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>