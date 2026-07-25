<!--
	* Nombre del fichero: login.php
	* Descripción: Formulario de inicio de sesión para el sistema de inventario
	*
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	*
	* Parámetros de entrada: 
	*   - login_usuario, login_clave
	* 
	* Salida: Ninguno
-->


<div class="main-container">

	<form class="box login" action="" method="POST" autocomplete="off">
		<h5 class="title is-5 has-text-centered is-uppercase">Sistema de inventario</h5>

		<div class="field">
			<label class="label">Usuario</label>
			<div class="control">
				<input class="input" type="text" name="login_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required >
			</div>
		</div>

		<div class="field">
			<label class="label">Clave</label>
			<div class="control">
				<input class="input" type="password" name="login_clave" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
			</div>
		</div>

		<p class="has-text-centered mb-4 mt-3">
			<button type="submit" class="button is-info is-rounded">Iniciar sesion</button>
		</p>

		<?php
			// Verificar si se han enviado los datos del formulario
			if(isset($_POST['login_usuario']) && isset($_POST['login_clave'])){
				// Incluir archivos necesarios para iniciar sesión
				require_once "./php/main.php";
				require_once "./php/iniciar_sesion.php";
			}
		?>
	</form>
</div>