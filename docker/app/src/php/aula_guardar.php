<?php
    /**
     * Nombre del fichero: aula_guardar.php
     * Descripción: Guarda los datos de un aula en la base de datos.
     *
     * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
     * Fecha: Marzo 2025
     *
     * Parámetros de entrada: 
     *   - aula_nombre: Nombre del aula (string)
     *   - aula_ubicacion: Ubicación del aula (string)
     *   - aula_descripcion: Descripción del aula (string)
     * 
     * Parámetros de salida: Ninguno
     */

require_once "main.php";

// Almacenando datos
$nombre = limpiar_cadena($_POST['aula_nombre']);
$ubicacion = limpiar_cadena($_POST['aula_ubicacion']);
$descripcion = limpiar_cadena($_POST['aula_descripcion']);

// Verificando campos obligatorios
if ($nombre == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    exit();
}

// Verificando integridad de los datos
if (verificar_datos("A[0-9]{3}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if ($ubicacion != "") {
    if (verificar_datos("E[1-4]-P[0-3]", $ubicacion)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La UBICACION no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
}

if ($descripcion != "") {
    if (verificar_datos("[A-Z][a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,24}", $descripcion)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                la DESCRIPCION no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
}

// Verificando nombre
$check_nombre = conexion();
$check_nombre = $check_nombre->query("SELECT aula_nombre FROM t_aula WHERE aula_nombre='$nombre'");
if ($check_nombre->rowCount() > 0) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
        </div>
    ';
    exit();
}
$check_nombre = null;

// Guardando datos
$guardar_aula = conexion();
$guardar_aula = $guardar_aula->prepare("INSERT INTO t_aula(aula_nombre, aula_ubicacion, aula_descripcion) VALUES(:nombre, :ubicacion, :descripcion)");

$marcadores = [
    ":nombre" => $nombre,
    ":ubicacion" => $ubicacion,
    ":descripcion" => $descripcion
];

$guardar_aula->execute($marcadores);

if ($guardar_aula->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡aula REGISTRADA!</strong><br>
            El aula se registro con exito
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo registrar el aula, por favor intente nuevamente
        </div>
    ';
}
$guardar_aula = null;
