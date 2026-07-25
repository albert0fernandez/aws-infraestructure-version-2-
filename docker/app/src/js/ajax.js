/**
* @file ajax.js
* @description Este archivo contiene la funcionalidad para enviar formularios mediante AJAX.
* Se seleccionan todos los formularios con la clase "FormularioAjax" y se les añade
* un evento de envío que realiza una solicitud fetch con los datos del formulario.
*
* @autor RetaCantabria - ASIR1 -- Dpto. Informática - IES Alisal 
* @fecha marzo de 2025
* 
* Parámetros de entrada:
*   - e: El evento de envío del formulario.
* 
* Salida:
*   - Actualiza el contenido del contenedor con la clase "form-rest" con la respuesta del servidor.
*/

const formularios_ajax = document.querySelectorAll(".FormularioAjax");

/**
* Función que maneja el envío del formulario mediante AJAX.
* @param {Event} e - El evento de envío del formulario.
*/
function enviar_formulario_ajax(e) {
    e.preventDefault();

    // Confirmación para enviar el formulario
    let enviar = confirm("¿Quieres enviar el formulario?");

    if (enviar == true) {

        let data = new FormData(this);
        let method = this.getAttribute("method");
        let action = this.getAttribute("action");

        let encabezados = new Headers();

        let config = {
            method: method,
            headers: encabezados,
            mode: 'cors',
            cache: 'no-cache',
            body: data
        };

        // Enviar la solicitud usando fetch
        fetch(action, config)
            .then(respuesta => respuesta.text())
            .then(respuesta => {
                let contenedor = document.querySelector(".form-rest");
                contenedor.innerHTML = respuesta;
            });
    }
}

// Añadir el evento de envío a cada formulario
formularios_ajax.forEach(formularios => {
    formularios.addEventListener("submit", enviar_formulario_ajax);
});