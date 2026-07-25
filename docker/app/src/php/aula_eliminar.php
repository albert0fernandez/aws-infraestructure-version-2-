<?php
	/**
	 * Nombre del fichero: aula_eliminar.php
	 * Descripción: Elimina un aula de la base de datos si no tiene recursos asociados.
	 *
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 *
	 * Parámetros de entrada: 
	 *   - room_id_del: ID del aula a eliminar.
	 * 
	 * Salida: 
	 *    - Mensaje de éxito o error
	 */

	// Almacenando datos
	$room_id_del=limpiar_cadena($_GET['room_id_del']);

	// Verificando usuario
	$check_aula=conexion();
	$check_aula=$check_aula->query("SELECT aula_id FROM t_aula WHERE aula_id='$room_id_del'");

	if($check_aula->rowCount()==1){

		$check_recursos=conexion();
		$check_recursos=$check_recursos->query("SELECT aula_id FROM t_recurso WHERE aula_id='$room_id_del' LIMIT 1");

		if($check_recursos->rowCount()<=0){

			$eliminar_aula=conexion();
			$eliminar_aula=$eliminar_aula->prepare("DELETE FROM t_aula WHERE aula_id=:id");

			$eliminar_aula->execute([":id"=>$room_id_del]);

			if($eliminar_aula->rowCount()==1){
				echo '
					<div class="notification is-info is-light">
						<strong>¡aula ELIMINADA!</strong><br>
						Los datos de el aula se eliminaron con exito
					</div>
				';
			}else{
				echo '
					<div class="notification is-danger is-light">
						<strong>¡Ocurrio un error inesperado!</strong><br>
						No se pudo eliminar el aula, por favor intente nuevamente
					</div>
				';
			}
			$eliminar_aula=null;
		}else{
			echo '
				<div class="notification is-danger is-light">
					<strong>¡Ocurrio un error inesperado!</strong><br>
					No podemos eliminar el aula ya que tiene recursos asociados
				</div>
			';
		}
		$check_recursos=null;
	}else{
		echo '
			<div class="notification is-danger is-light">
				<strong>¡Ocurrio un error inesperado!</strong><br>
				La aula que intenta eliminar no existe
			</div>
		';
	}
	$check_aula=null;
?>