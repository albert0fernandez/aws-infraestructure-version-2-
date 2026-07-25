<!--
	* Nombre del fichero: departamento_new.php
	* Descripción: Formulario para la creación de un nuevo departamento
	*
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	*
	* Parámetros de entrada: Ninguno
	* Salida: Ninguno
-->

<div class="container is-fluid mb-6">
	<h1 class="title">Departamentos</h1>
	<h2 class="subtitle">Nuevo departamento</h2>
</div> 

<div class="container pb-6 pt-6">

	<div class="form-rest mb-6 mt-6"></div>

	<form action="./php/departamento_guardar.php" method="POST" class="FormularioAjax" autocomplete="off" >
		<div class="columns">
			<div class="column">
				<div class="control">
					<!-- Nombre del departamento -->
					<label>Nombre</label>
					<input class="input" type="text" name="departamento_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}" maxlength="40" placeholder="Ingresa un nombre para el departamento" title="Ingresa un nombre para el departamento" required />
				</div>
			</div>
			<div class="column">
				<div class="control">
					<!-- Ubicacion del departamento -->
					<label>Ubicacion</label>
					<input class="input" type="text" name="departamento_ubicacion" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}" maxlength="40" placeholder="Ejemplo: E1-P2" title="Ingresa una ubicacion para el departamento" required />
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<!--  responsable del departamento -->
					<label>Usuario</label>
					<input class="input" type="text" name="responsable_id" pattern="[a-z0-9]{4,20}" maxlength="20" placeholder="Escoge al Usuario Responsable" title="Ingresa al Usuario Responsable" required />
				</div>
			</div>
		</div>
		
		<p class="has-text-centered">
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>
</div>