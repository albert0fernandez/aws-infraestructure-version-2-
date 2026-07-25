<!--
	* Nombre del fichero: room_new.php
	* Descripción: Formulario para crear una nueva aula
	*
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	* 
	* Parámetros de entrada: Ninguno
	* Salida: Ninguno
-->


<div class="container is-fluid mb-6">
	<h1 class="title">Aulas</h1>
	<h2 class="subtitle">Nueva aula</h2>
</div>

<div class="container pb-6 pt-6">

<div class="form-rest mb-6 mt-6"></div>

<form action="./php/aula_guardar.php" method="POST" class="FormularioAjax" autocomplete="off" >
	<div class="columns">
		<div class="column">
			<div class="control">
				<!-- Nombre del aula -->
				<label>Nombre</label>
				<input class="input" type="text" name="aula_nombre" pattern="A[0-9]{3}" maxlength="4" placeholder="A[0-9]{3}" required />
			</div>
		</div>

		<div class="column">
			<div class="control">
				<!-- Ubicación del aula -->
				<label>Ubicación</label>
				<input class="input" type="text" name="aula_ubicacion" pattern="E[0-4]-P[0-3]" maxlength="5" placeholder="Ee-Pp, siendo e={1,2,3,4} y p={1,2,3}" title="EePp, siendo e={1,2,3,4} y p={1,2,3}"/>
			</div>
		</div>
		<div class="column">
			<div class="control">
				<!-- Descripción del aula -->
				<label>Descripción</label>								
				<input class="input" type="text" name="aula_descripcion" pattern="[A-Z][a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,24}" maxlength="25" placeholder="[A-Z][a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,24}" title="Cadena de entre 5 y 24 letras o dígitos. empieza por mayúscula"/>
			</div>
		</div>
	</div>

	<p class="has-text-centered">
		<button class="button is-info is-rounded has-text-centered" type="submit">Guardar</button>
	</p>

</form>
</div>