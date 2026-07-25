<?php
	/**
	 * recurso_eliminar.php
	 * 
	 * Este script elimina un recurso de la base de datos y su imagen asociada del servidor.
	 * 
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 * 
	 * Parámetros de entrada:
	 * - resource_id_del: ID del recurso a eliminar (vía GET)
	 * 
	 * Parámetros de salida:
	 * - Mensaje de éxito o error en formato HTML
	 */

	// Almacenando datos
	$resource_id_del = limpiar_cadena($_GET['resource_id_del']);

	// Verificando recurso
	$check_recurso = conexion();
	$check_recurso = $check_recurso->query("SELECT * FROM t_recurso WHERE recurso_id='$resource_id_del'");

	if ($check_recurso->rowCount() == 1) {
		$datos = $check_recurso->fetch();

		$eliminar_recurso = conexion();
		$eliminar_recurso = $eliminar_recurso->prepare("DELETE FROM t_recurso WHERE recurso_id=:id");

		$eliminar_recurso->execute([":id" => $resource_id_del]);

		if ($eliminar_recurso->rowCount() == 1) {
			if (is_file("./img/recurso/" . $datos['recurso_foto'])) {
				chmod("./img/recurso/" . $datos['recurso_foto'], 0777);
				unlink("./img/recurso/" . $datos['recurso_foto']);
			}

			echo '
				<div class="notification is-info is-light">
					<strong>¡recurso ELIMINADO!</strong><br>
					Los datos del recurso se eliminaron con exito
				</div>
			';
		} else {
			echo '
				<div class="notification is-danger is-light">
					<strong>¡Ocurrio un error inesperado!</strong><br>
					No se pudo eliminar el recurso, por favor intente nuevamente
				</div>
			';
		}
		$eliminar_recurso = null;
	} else {
		echo '
			<div class="notification is-danger is-light">
				<strong>¡Ocurrio un error inesperado!</strong><br>
				El recurso que intenta eliminar no existe
			</div>
		';
	}
	$check_recurso = null;