/**
* @file validar_clave.js
* @description Este archivo contiene la funcionalidad para validar contraseñas ingresadas por el usuario.
* Verifica que las contraseñas coincidan y que cumplan con los requisitos de fortaleza.
*
* @autor RetaCantabria - ASIR1 -- Dpto. Informática - IES Alisal 
* @fecha marzo de 2025
* 
* Parámetros de entrada:
*   - Ninguno
* 
* Salida:
*   - Retorna true si la contraseña es válida, de lo contrario false.
*/

/**
 * Función para validar la contraseña ingresada por el usuario.
 * Verifica que las contraseñas coincidan y que cumplan con los requisitos de fortaleza.
 * 
 * @returns {boolean} - Retorna true si la contraseña es válida, de lo contrario false.
 */
function validar_clave() {
    // Obtener los valores de las contraseñas ingresadas
    var clave1 = document.querySelector('input[name="usuario_clave_1"]').value;
    var clave2 = document.querySelector('input[name="usuario_clave_2"]').value;

    // Verificar que las contraseñas coincidan
    if (clave1 !== clave2) {
        alert("Las contraseñas no coinciden. Por favor, inténtelo de nuevo.");
        return false;
    }

    // Verificar la fortaleza de la contraseña (tiene al menos una minúscula, una mayúscula, un dígito, un carácter especial "$", "@"", ".", o "-" y al menos 7 caracteres)
    if (clave1 !== "") {
        var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@.-]).{7,100}$/;
        if (!regex.test(clave1)) {
            alert("La contraseña debe tener al menos 7 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial ($@.-).");
            return false;
        }
    }

    return true;
}