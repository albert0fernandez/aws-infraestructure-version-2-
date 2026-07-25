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
	<h1 class="title">Mantenimiento</h1>
	<h2 class="subtitle">Nuevo Mantenimiento</h2>
</div> 

<div class="container pb-6 pt-6">

	<div class="form-rest mb-6 mt-6"></div>

	<form action="./php/departamento_guardar.php" method="POST" class="FormularioAjax" autocomplete="off" >
		<div class="columns">
			<div class="column">
				<div class="control">
					<!-- Usuario Id -->
					<label>Nombre del Usuario</label>
					<input class="input" type="text" name="proveedor_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}" maxlength="40" placeholder="Escoge nombre del Usuario" title="Ingresa un nombre para el proveedor" required />
				</div>
			</div>
			<div class="column">
				<div class="control">
					<!-- Recurso ID -->
					<label>Nombre del Recurso</label>
					<input class="input" type="text" name="proveedor_telefono" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}" maxlength="40" placeholder="Escoge nombre del Recurso" title="Ingresa un telefono para el proveedor" required />
				</div>
			</div>
		</div>
				
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Fecha y hora de inicio</label><br>
                    <input type="datetime-local" name="uso_fecha_inicio" min="2022-01-01T07:00" max="<?php echo $currentDateTime ?>"  required />
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Fecha y hora de fin</label><br>
                    <input type="datetime-local" name="uso_fecha_fin"  min="2022-01-0107:00" max="<?php echo $currentDateTime ?>" />
                </div>
            </div>
        </div>    

		<p class="has-text-centered">
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>
</div>

