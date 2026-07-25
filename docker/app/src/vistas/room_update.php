<!--
	* Nombre del fichero: room_update.php
	* Descripción: Formulario para actualizar la información de un aula.
	* 
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	* 
	* Parámetros de entrada: 
	*   - room_id_up: ID del aula a actualizar
	*
	* Parámetros de salida: Ninguno
-->

<div class="container is-fluid mb-6">
	<h1 class="title">Aulas</h1>
	<h2 class="subtitle">Actualizar aula</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
		include "./inc/btn_back.php";

		require_once "./php/main.php";

		$id = (isset($_GET['room_id_up'])) ? $_GET['room_id_up'] : 0;
		$id=limpiar_cadena($id);

		// Verificando aula
		$check_aula=conexion();
		$check_aula=$check_aula->query("SELECT * FROM t_aula WHERE t_aula.aula_id='$id'");

		if($check_aula->rowCount()>0){
			$datos=$check_aula->fetch();
	?>

	<div class="form-rest mb-6 mt-6"></div>

	<h2 class="title has-text-centered"><?php echo $datos['aula_nombre']; ?></h2>

	<form action="./php/aula_actualizar.php" method="POST" class="FormularioAjax" autocomplete="off" >

		<input type="hidden" name="aula_id" value="<?php echo $datos['aula_id']; ?>" required >

		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Nombre</label>
					<input class="input" type="text" name="aula_nombre" pattern="A[0-9]{3}" maxlength="4" required value="<?php echo $datos['aula_nombre']; ?>" >
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Ubicación</label>
					<input class="input" type="text" name="aula_ubicacion" pattern="E[0-4]-P[0-3]" maxlength="5" placeholder="Ee-Pp, siendo e={1,2,3,4} y p={1,2,3}" title="EePp, siendo e={1,2,3,4} y p={1,2,3}" value="<?php echo $datos['aula_ubicacion']; ?>" >
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Descripción</label>
					<input class="input" type="text" name="aula_descripcion" pattern="[A-Z][a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,24}" maxlength="25" value="<?php echo $datos['aula_descripcion']; ?>" placeholder="[A-Z][a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,24}" title="Cadena de entre 5 y 24 letras o dígitos. empieza por mayúscula" >
				</div>
			</div>

		</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded">Actualizar</button>
		</p>
	</form>
	<?php 
		}else{
			include "./inc/error_alert.php";
		}
		$check_aula=null;
	?>
</div>