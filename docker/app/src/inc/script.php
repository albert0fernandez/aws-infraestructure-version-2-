<!--
 * Nombre del fichero: script.php
 * Descripción: Script para manejar la funcionalidad de la barra de navegación.
 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
 * Fecha: Marzo 2025
 * Parámetros de entrada: Ninguno
 * Parámetros de salida: Ninguno
-->

<script>
    document.addEventListener('DOMContentLoaded', () => {

        // Obtener todos los elementos "navbar-burger"
        const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

        // Verificar si hay algún "navbar-burger"
        if ($navbarBurgers.length > 0) {

            // Agregar un evento de clic a cada uno de ellos
            $navbarBurgers.forEach( el => {
                    el.addEventListener('click', () => {

                    // Obtener el objetivo del atributo "data-target"
                    const target = el.dataset.target;
                    const $target = document.getElementById(target);

                    // Alternar la clase "is-active" tanto en el "navbar-burger" como en el "navbar-menu"
                    el.classList.toggle('is-active');
                    $target.classList.toggle('is-active');
                });
            });
        }
    });
</script>
<script src="./js/ajax.js"></script> 