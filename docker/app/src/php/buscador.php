<?php
		
	/**
	 * Nombre del fichero: buscador.php
	 * Descripción: Este script maneja las búsquedas y eliminaciones de términos de búsqueda en diferentes módulos.
	 *
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 *
	 */
	
	ob_start();
	
	$modulo_buscador=limpiar_cadena($_POST['modulo_buscador']);

	$modulos=["usuario","aula","recurso","historial_uso","mantenimiento","departamento","proveedor"];

	if(in_array($modulo_buscador, $modulos)){

		$modulos_url=[
			"usuario"=>"user_search",
			"aula"=>"room_search",
			"recurso"=>"resource_search",
			"historial_uso"=>"history_use_search",
			"mantenimiento"=>"maintenance_search",
			"departamento"=>"department_search",
			"proveedor"=>"supplier_search"
		];

		$modulos_url=$modulos_url[$modulo_buscador];

		$modulo_buscador="busqueda_".$modulo_buscador;


		// Iniciar busqueda 
		if(isset($_POST['txt_buscador'])){
			$txt=limpiar_cadena($_POST['txt_buscador']);

			if($txt==""){
				echo '
					<div class="notification is-danger is-light">
						<strong>¡Ocurrio un error inesperado!</strong><br>
						Introduce el termino de busqueda
					</div>
				';
			}else{
				if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,50}",$txt)){
					echo '
						<div class="notification is-danger is-light">
							<strong>¡Ocurrio un error inesperado!</strong><br>
							El termino de busqueda no coincide con el formato solicitado
						</div>
					';
				}else{
					$_SESSION[$modulo_buscador]=$txt;	
					header("Location: index.php?vista=$modulos_url",true,303); 
					ob_end_flush();
					exit();  
				}
			}
		}


		// Eliminar busqueda
		if(isset($_POST['eliminar_buscador'])){
			unset($_SESSION[$modulo_buscador]);
			header("Location: index.php?vista=$modulos_url",true,303); 
			ob_end_flush();
			exit();
		}
	}else{
		echo '
			<div class="notification is-danger is-light">
				<strong>¡Ocurrio un error inesperado!</strong><br>
				No podemos procesar la peticion
			</div>
		';
	}
	ob_end_flush();
?>