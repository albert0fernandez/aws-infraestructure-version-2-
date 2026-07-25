<?php
	/**
	 * Nombre del fichero: proveedor_eliminar.php
	 * Descripción: Elimina un proveedor de la base de datos si no tiene recursos asociados.
	 *
	 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	 * Fecha: Marzo 2025
	 *
	 * Parámetros de entrada: 
	 *   - supplier_id_del: ID del proveedor a eliminar.
	 * 
	 * Salida: 
	 *    - Mensaje de éxito o error
	 */

	// Almacenando datos
	$supplier_id_del=limpiar_cadena($_GET['supplier_id_del']);

	// Verificando proveedor
	$check_proveedor=conexion();
	$check_proveedor=$check_proveedor->query("SELECT proveedor_id FROM t_proveedor WHERE proveedor_id='$supplier_id_del'");

	if($check_proveedor->rowCount()==1){

		// Verificando si el proveedor tiene recursos asociados
		$check_recursos=conexion();
		$check_recursos=$check_recursos->query("SELECT recurso_id FROM t_recurso WHERE proveedor_id='$supplier_id_del' LIMIT 1");

		if($check_recursos->rowCount()<=0){

			$eliminar_proveedor=conexion();
			$eliminar_proveedor=$eliminar_proveedor->prepare("DELETE FROM t_proveedor WHERE proveedor_id=:id");

			$eliminar_proveedor->execute([":id"=>$supplier_id_del]);

			if($eliminar_proveedor->rowCount()==1){
				echo '
					<div class="notification is-info is-light">
						<strong>¡Proveedor ELIMINADO!</strong><br>
						Los datos del proveedor se eliminaron con exito
					</div>
				';
			}else{
				echo '
					<div class="notification is-danger is-light">
						<strong>¡Ocurrio un error inesperado!</strong><br>
						No se pudo eliminar el proveedor, por favor intente nuevamente
					</div>
				';
			}
			$eliminar_proveedor=null;
		}else{
			echo '
				<div class="notification is-danger is-light">
					<strong>¡Ocurrio un error inesperado!</strong><br>
					No podemos eliminar el proveedor ya que tiene recursos asociados
				</div>
			';
		}
		$check_recursos=null;
	}else{
		echo '
			<div class="notification is-danger is-light">
				<strong>¡Ocurrio un error inesperado!</strong><br>
				El proveedor que intenta eliminar no existe
			</div>
		';
	}
	$check_aula=null;
?>