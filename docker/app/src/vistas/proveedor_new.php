<!--
	* Nombre del fichero: proveedor_new.php
	* Descripción: Formulario para la creación de un nuevo departamento
	*
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	*
	* Parámetros de entrada: Ninguno
	* Salida: Ninguno
-->

<div class="container is-fluid mb-6">
	<h1 class="title">Proveedor</h1>
	<h2 class="subtitle">Nuevo Proveedor</h2>
</div> 

<div class="container pb-6 pt-6">

	<div class="form-rest mb-6 mt-6"></div>

	<form action="./php/departamento_guardar.php" method="POST" class="FormularioAjax" autocomplete="off" >
		<div class="columns">
			<div class="column">
				<div class="control">
					<!-- Nombre del proveedor -->
					<label>Nombre</label>
					<input class="input" type="text" name="proveedor_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}" maxlength="40" placeholder="Ingresa un nombre para el proveedor" title="Ingresa un nombre para el proveedor" required />
				</div>
			</div>
			<div class="column">
				<div class="control">
					<!-- telefono del proveedor -->
					<label>Telefono</label>
					<input class="input" type="text" name="proveedor_telefono" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}" maxlength="40" placeholder="Ejemplo: 123456789" title="Ingresa un telefono para el proveedor" required />
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<!--  email del proveedor -->
					<label>Email</label>
					<input class="input" type="email" name="proveedor_email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" maxlength="30" placeholder="ejemplo@dominio.com" title="Ingresa un email para el proveedor" required/>
				</div>
			</div>
		</div>
		
		<p class="has-text-centered">
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>
</div>

