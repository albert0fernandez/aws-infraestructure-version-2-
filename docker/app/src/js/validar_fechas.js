/**
* @file validar_fechas.js
* @description Este archivo contiene la funcionalidad para validar las fechas de uso de un aula.
*
* @autor RetaCantabria - ASIR1 -- Dpto. Informática - IES Alisal 
* @fecha marzo de 2025
* 
* Parámetros de entrada:
* - Ninguno.
* 
* Salida:
* - Mensajes de validación de las fechas.
*/

/**
* Función para validar las fechas de uso de un aula.
* @returns {boolean} - Indica si las fechas son válidas o no.
*/
function validarFechas() {
    var fechaInicio = document.querySelector('input[name="uso_fecha_inicio"]').value;
    var fechaFin = document.querySelector('input[name="uso_fecha_fin"]').value;
    var fechaActual = new Date();

    var fechaInicioDate = new Date(fechaInicio);
    var fechaFinDate = new Date(fechaFin);

    // Verificar que la fecha de inicio no sea posterior a la fecha actual
    if (fechaInicioDate > fechaActual) {
        alert('La fecha y hora de inicio no pueden ser posteriores a la fecha y hora actual. Por favor, inténtelo de nuevo.');
        return false;
    }

    // Verificar que la fecha de fin sea posterior a la fecha de inicio
    if (fechaInicioDate >= fechaFinDate) {
        alert('La fecha y hora de fin deben ser posteriores a la fecha y hora de inicio. Por favor, inténtelo de nuevo.');
        return false;
    }

    // Verificar que las fechas no sean fines de semana
    if (esFinDeSemana(fechaInicio) || esFinDeSemana(fechaFin)) {
        alert('Las fechas no pueden ser en fin de semana. Por favor, seleccione fechas válidas.');
        return false;
    }

    // Verificar que las fechas no estén en el rango de 20:30 a 8:00
    if (esHoraInvalida(fechaInicioDate) || esHoraInvalida(fechaFinDate)) {
        alert('Las horas de uso han de estar comprendidas entre las 8:00 y las 20:30. Por favor, seleccione horas válidas.');
        return false;
    }

    return true;
}

/**
* Función para verificar si una fecha cae en fin de semana.
* @param {string} fecha - La fecha a verificar.
* @returns {boolean} - Indica si la fecha cae en fin de semana.
*/
function esFinDeSemana(fecha) {
    var dia = new Date(fecha).getDay();
    return dia === 0 || dia === 6; // 0 = Domingo, 6 = Sábado
}

/**
* Función para verificar si una hora es inválida (fuera del rango de 8:00 a 20:30).
* @param {Date} fecha - La fecha a verificar.
* @returns {boolean} - Indica si la hora es inválida.
*/
function esHoraInvalida(fecha) {
    var hora = fecha.getHours();
    var minuto = fecha.getMinutes();
    return (hora >= 20 && minuto >= 30) || (hora < 8);
}