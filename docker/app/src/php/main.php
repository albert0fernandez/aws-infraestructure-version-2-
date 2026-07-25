<?php
	/**
	 * Nombre del fichero: main.php
	 * Descripción: Funciones para la gestión de la base de datos y validación de XML.
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 * 
	 * Parámetros de entrada:
	 *    - Dependiendo de la función.
	 * 
	 * Parámetros de salida:
	 *    - Dependiendo de la función.
	 */
	
	// Conexión a la base de datos
	function conexion(){
		// Config por variables de entorno (Docker/AWS); fallback = valores originales
		$host = getenv('DB_HOST') ?: '127.0.0.1';
		$name = getenv('DB_NAME') ?: 'pdo_solucion';
		$user = getenv('DB_USER') ?: 'userCRUD';
		$pass = getenv('DB_PASSWORD') ?: 'CRUD@1234';
		$pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass);
		return $pdo;
	}

	
	function conexionXML($cadena)
	{
		$pdoXML=null;
		// Insertar código para que mysql devuelva los resultados de las consultas en xml.
		
		$comando="mysql -u " . (getenv('DB_USER') ?: 'userCRUD') . " -p" . (getenv('DB_PASSWORD') ?: 'CRUD@1234') . " -h " . (getenv('DB_HOST') ?: '127.0.0.1') . " --xml -e " . $cadena . " " . (getenv('DB_NAME') ?: 'pdo_solucion');
		$salida="";
		exec($comando,$salida,$pdoXML);
		echo $salida;
		return $salida;	
	}

	// Validar XML contra XSD
	function validarXML($xmlNombre) 
	{
		$xml = new DOMDocument();
		$root_path = $_SERVER['DOCUMENT_ROOT'] . '/IESAlisal24RetaCantabriaASIR1-Solucion/INVENTARIO-main/';
		$ruta_xml= 'xml/recursos/';
		$ruta_xsd= 'schemas/';
		$xml->load($root_path.$ruta_xml.$xmlNombre);

		if ($xml->schemaValidate($root_path .'schemas/validacion_recursos.xsd')) {
			return true;		
		} else {
			return false;
		}
	}

	// Verificar datos
	function verificar_datos($filtro,$cadena){
		if(preg_match("/^".$filtro."$/", $cadena)){
			return false;
		}else{
			return true;
		}
	}

	// Verificar fechas
	function verificar_fechas($fecha_inicio, $fecha_fin) {
		$fecha_actual = new DateTime();
		$fecha_inicio_dt = new DateTime($fecha_inicio);
		$fecha_fin_dt = new DateTime($fecha_fin);
		$fecha_maxima = (clone $fecha_actual)->add(new DateInterval('P3M'));

		// Verificar que la fecha de inicio no sea posterior a la fecha actual
		if ($fecha_inicio_dt > $fecha_actual) {
			return 'La fecha de inicio no puede ser posterior a la fecha actual.';
		}

		// Verificar que la fecha de fin sea posterior a la fecha de inicio
		if ($fecha_fin_dt <= $fecha_inicio_dt) {
			return 'La fecha de fin debe ser posterior a la fecha de inicio.';
		}

		// Verificar que la fecha de fin no sea posterior a la fecha máxima permitida (fecha actual + 3 meses)
		if ($fecha_fin_dt > $fecha_maxima) {
			return 'La fecha de fin no puede ser posterior a la fecha actual más tres meses.';
		}

		// Verificar que las fechas no sean fines de semana
		if ($fecha_inicio_dt->format('N') >= 6 || $fecha_fin_dt->format('N') >= 6) {
			return 'Las fechas no pueden ser en fin de semana.';
		}

		// Verificar que las horas están entre las 8:00 y las 20:30
		$hora_inicio = (int)$fecha_inicio_dt->format('H');
		$minuto_inicio = (int)$fecha_inicio_dt->format('i');
		$hora_fin = (int)$fecha_fin_dt->format('H');
		$minuto_fin = (int)$fecha_fin_dt->format('i');

		if (($hora_inicio >= 20 && $minuto_inicio >= 30) || ($hora_inicio < 8) || ($hora_fin >= 20 && $minuto_fin >= 30) || ($hora_fin < 8)) {
			return 'El horario de uso ha de estar entre las 8:00 y las 20:30.';
		}
		
		return true;
	}

	// Limpiar cadenas de texto
	function limpiar_cadena($cadena){
		$cadena=trim($cadena);
		$cadena=stripslashes($cadena);
		$cadena=str_ireplace("<script>", "", $cadena);
		$cadena=str_ireplace("</script>", "", $cadena);
		$cadena=str_ireplace("<script src", "", $cadena);
		$cadena=str_ireplace("<script type=", "", $cadena);
		$cadena=str_ireplace("SELECT * FROM", "", $cadena);
		$cadena=str_ireplace("DELETE FROM", "", $cadena);
		$cadena=str_ireplace("INSERT INTO", "", $cadena);
		$cadena=str_ireplace("DROP TABLE", "", $cadena);
		$cadena=str_ireplace("DROP DATABASE", "", $cadena);
		$cadena=str_ireplace("TRUNCATE TABLE", "", $cadena);
		$cadena=str_ireplace("SHOW TABLES;", "", $cadena);
		$cadena=str_ireplace("SHOW DATABASES;", "", $cadena);
		$cadena=str_ireplace("<?php", "", $cadena);
		$cadena=str_ireplace("?>", "", $cadena);
		$cadena=str_ireplace("--", "", $cadena);
		$cadena=str_ireplace("^", "", $cadena);
		$cadena=str_ireplace("<", "", $cadena);
		$cadena=str_ireplace("[", "", $cadena);
		$cadena=str_ireplace("]", "", $cadena);
		$cadena=str_ireplace("==", "", $cadena);
		$cadena=str_ireplace(";", "", $cadena);
		$cadena=str_ireplace("::", "", $cadena);
		$cadena=trim($cadena);
		$cadena=stripslashes($cadena);
		return $cadena;
	}

	// Crear directorios
	function crear_directorio($directorio){
		if(!file_exists($directorio)){
			if(!mkdir($directorio, 0777)){
				echo '
					<div class="notification is-danger is-light">
						<strong>¡Ocurrio un error inesperado!</strong><br>
						Error al crear el directorio.
					</div>
				';
				exit();
			}
		}
	}

	// Renombrar fotos
	function renombrar_fotos($nombre){
		$nombre=str_ireplace(" ", "_", $nombre);
		$nombre=str_ireplace("/", "_", $nombre);
		$nombre=str_ireplace("#", "_", $nombre);
		$nombre=str_ireplace("-", "_", $nombre);
		$nombre=str_ireplace("$", "_", $nombre);
		$nombre=str_ireplace(".", "_", $nombre);
		$nombre=str_ireplace(",", "_", $nombre);
		$nombre=$nombre."_".rand(0,100);
		return $nombre;
	}

	// Renombrar XML
	function nombrar_xml($nombre){
		$nombre=str_ireplace(" ", "_", $nombre);
		$nombre=str_ireplace("/", "_", $nombre);
		$nombre=str_ireplace("#", "_", $nombre);
		$nombre=str_ireplace("$", "_", $nombre);
		$nombre=str_ireplace(".", "_", $nombre);
		$nombre=str_ireplace(",", "_", $nombre);

		$fecha_hora = date("YmdHis");
		$nombre = $nombre.'-'.$_COOKIE["INV"].'-'.$fecha_hora;

		return $nombre;
	}

	// Paginador de tablas
	function paginador_tablas($pagina,$Npaginas,$url,$botones){
		$tabla='<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';

		if($pagina<=1){
			$tabla.='
			<a class="pagination-previous is-disabled" disabled >Anterior</a>
			<ul class="pagination-list">';
		}else{
			$tabla.='
			<a class="pagination-previous" href="'.$url.($pagina-1).'" >Anterior</a>
			<ul class="pagination-list">
				<li><a class="pagination-link" href="'.$url.'1">1</a></li>
				<li><span class="pagination-ellipsis">&hellip;</span></li>
			';
		}

		$ci=0;
		for($i=$pagina; $i<=$Npaginas; $i++){
			if($ci>=$botones){
				break;
			}
			if($pagina==$i){
				$tabla.='<li><a class="pagination-link is-current" href="'.$url.$i.'">'.$i.'</a></li>';
			}else{
				$tabla.='<li><a class="pagination-link" href="'.$url.$i.'">'.$i.'</a></li>';
			}
			$ci++;
		}

		if($pagina==$Npaginas){
			$tabla.='
			</ul>
			<a class="pagination-next is-disabled" disabled >Siguiente</a>
			';
		}else{
			$tabla.='
				<li><span class="pagination-ellipsis">&hellip;</span></li>
				<li><a class="pagination-link" href="'.$url.$Npaginas.'">'.$Npaginas.'</a></li>
			</ul>
			<a class="pagination-next" href="'.$url.($pagina+1).'" >Siguiente</a>
			';
		}

		$tabla.='</nav>';
		return $tabla;
	}
