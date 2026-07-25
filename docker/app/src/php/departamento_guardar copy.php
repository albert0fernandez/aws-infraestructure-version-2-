<?php
    /**
     * Nombre del fichero: departamento_guardar.php
     * Descripción: Guarda los datos de un departamento en la base de datos.
     *
     * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
     * Fecha: Mayo 2025
     *
     * Parámetros de entrada: 
     *   - departamento_nombre: Nombre del aula (string)
     *   - departamento_ubicacion: Ubicación física del departamento (string)
     *   - departamento_responsable: Jefe de departamento, ha de ser usuario del inventario (int)
     * 
     * Parámetros de salida: Ninguno
     */

require_once "main.php";

// Almacenando datos
$nombre = limpiar_cadena($_POST['departamento_nombre']);
$ubicacion = limpiar_cadena($_POST['departamento_ubicacion']);
$responsable = limpiar_cadena($_POST['departamento_responsable']);

// Verificando campos obligatorios
if($nombre == "") {
    echo '
        <div class="notification is-danger is-light">
         <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </divlass=>
    ';
    exit();
}

// Verificando integridad de los datos
if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,40}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if($ubicacion != "") {
    if (verificar_datos("E[0-4]-P[0-3]", $ubicacion)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La UBICACION no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
}

if($responsable != "") {
    if (verificar_datos("[0-9]{1,3}", $responsable)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                el RESPONSABLE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
}

// Verificando nombre
$check_nombre = conexion();
$check_nombre = $check_nombre->query("SELECT departamento_nombre FROM t_departamento WHERE departamento_nombre='$nombre'");
if($check_nombre->rowCount() > 0) {
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
$guardar_departamento = conexion();
$guardar_departamento = $guardar_departamento->prepare("INSERT INTO t_departamento(departamento_nombre, departamento_ubicacion, responsable_id) VALUES(:nombre, :ubicacion, :responsable)");

$marcadores = [
    ":nombre" => $nombre,
    ":ubicacion" => $ubicacion,
    ":responsable" => $responsable
];

$guardar_departamento->execute($marcadores);

if($guardar_departamento->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡departamento REGISTRADO!</strong><br>
            El departamento se registro con exito
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo registrar el departamento, por favor intente nuevamente
        </div>
    ';
}
$guardar_aula = null;
