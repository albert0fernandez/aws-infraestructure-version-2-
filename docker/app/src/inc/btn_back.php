<!--
 * Nombre del fichero: btn_back.php
 * Descripción: Este archivo contiene el código HTML y JavaScript para un botón que permite regresar a la página anterior.
 * 
 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
 * Fecha: Marzo 2025
 * 
 * Parámetros de entrada: Ninguno
 * Parámetros de salida: Ninguno
-->

<!-- Contenedor del botón de regreso -->
<p class="has-text-right pt-4 pb-4">
	<!-- Botón de regreso -->
	<a href="#" class="button is-link is-rounded btn-back"><- Regresar atrás</a>
</p>

<script type="text/javascript">
    // Selecciona el botón de regreso
    let btn_back = document.querySelector(".btn-back");

    // Agrega un evento de clic al botón de regreso
    btn_back.addEventListener('click', function(e){
        e.preventDefault(); // Previene el comportamiento por defecto del enlace
        window.history.back(); // Navega a la página anterior en el historial
    });
</script>