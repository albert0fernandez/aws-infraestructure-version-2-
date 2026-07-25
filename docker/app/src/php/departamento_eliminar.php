<?php
	/**
	 * Nombre del fichero: departamento_eliminar.php
	 * Descripción: Elimina un departamento de la base de datos si no tiene recursos asociados.
	 *
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 *
	 * Parámetros de entrada: 
	 *   - departamento_id_del: ID del departamento a eliminar.
	 * 
	 * Salida: 
	 *    - Mensaje de éxito o error
	 */

	// Almacenando datos
	$departamento_id_del=limpiar_cadena($_GET['department_id_del']);

	// Verificando departamento
	$check_departamento=conexion();
	$check_departamento=$check_departamento->query("SELECT departamento_id FROM t_departamento WHERE departamento_id='$departamento_id_del'");

	if($check_departamento->rowCount()==1){
			$eliminar_departamento=conexion();
			$eliminar_departamento=$eliminar_departamento->prepare("DELETE FROM t_departamento WHERE departamento_id=:id");

			$eliminar_departamento->execute([":id"=>$departamento_id_del]);

			if($eliminar_departamento->rowCount()==1){
				echo '
					<div class="notification is-info is-light">
						<strong>¡departamento ELIMINADO!</strong><br>
						Los datos de el departamento se eliminaron con exito
					</div>
				';
			}else{
				echo '
					<div class="notification is-danger is-light">
						<strong>¡Ocurrio un error inesperado!</strong><br>
						No se pudo eliminar el departamento, por favor intente nuevamente
					</div>
				';
			}
			$eliminar_departamento=null;
	}else{
		echo '
			<div class="notification is-danger is-light">
				<strong>¡Ocurrio un error inesperado!</strong><br>
				El departamento que intenta eliminar no existe
			</div>
		';
	}
	$check_departamento=null;
?>
