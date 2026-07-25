<!--
	* Nombre del fichero: user_new.php
	* Descripción: Formulario para la creación de un nuevo usuario
	*
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	*
	* Parámetros de entrada: Ninguno
	* Salida: Ninguno
-->

<div class="container is-fluid mb-6">
	<h1 class="title">Usuarios</h1>
	<h2 class="subtitle">Nuevo usuario</h2>
</div>

<div class="container pb-6 pt-6">

	<div class="form-rest mb-6 mt-6"></div>

	<form action="./php/usuario_guardar.php" method="POST" class="FormularioAjax" autocomplete="off" >
		<div class="columns">
			<div class="column">
				<div class="control">
					<!-- Nombre del usuario -->
					<label>Nombre</label>
					<input class="input" type="text" name="usuario_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}" maxlength="40" placeholder="Cadena de entre 3 y 40 letras con tilde" title="Cadena de entre 3 y 40 letras, acentos, ñ, Ñ, ü y Ü" required />
				</div>
			</div>
			<div class="column">
				<div class="control">
					<!-- Apellidos del usuario -->
					<label>Apellidos</label>
					<input class="input" type="text" name="usuario_apellido" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}" maxlength="40" placeholder="Cadena de entre 3 y 40 letras con tilde" title="Cadena de entre 3 y 40 letras, acentos, ñ, Ñ, ü y Ü" required />
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<!--  Nombre de usuario -->
					<label>Usuario</label>
					<input class="input" type="text" name="usuario_usuario" pattern="[a-z0-9]{4,20}" maxlength="20" placeholder="Cadena de entre 4 y 20 minúsculas y dígitos" title="Cadena de entre 4 y 20 minúsculas y dígitos" required />
				</div>
			</div>
			<div class="column">
				<div class="control">
					<!-- Email del usuario -->
					<label>Email</label>
					<input class="input" type="email" name="usuario_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}"  maxlength="70"  placeholder="Correo electrónico válido" title="Ingrese un correo electrónico válido" required />
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<!-- Clave del usuario -->
					<label>Clave</label>
					<input class="input" type="password" name="usuario_clave_1" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" placeholder="7 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial ($@.-)." title="La contraseña debe tener al menos 7 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial ($@.-)." required />
				</div>
			</div>
			<div class="column">
				<div class="control">
					<!-- Repetir clave del usuario -->
					<label>Repetir clave</label>
					<input class="input" type="password" name="usuario_clave_2" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required />
				</div>
			</div>
		</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>
</div>