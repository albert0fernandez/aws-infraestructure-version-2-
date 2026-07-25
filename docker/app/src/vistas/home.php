<!--
	* Nombre del fichero: home.php
	* Descripción: Vista principal de la página de inicio
	* 
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	* 
	* Parámetros de entrada: Ninguno
	* Salida: Ninguno
-->

<div class="container is-fluid">
	<h1 class="title">Home</h1>
	<h2 class="subtitle">¡Bienvenido <?php echo $_SESSION['nombre']." ".$_SESSION['apellido']; ?>!</h2>
</div>