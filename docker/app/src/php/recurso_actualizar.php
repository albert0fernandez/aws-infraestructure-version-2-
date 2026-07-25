<?php
/**
 * Nombre del fichero: recurso_actualizar.php
 * Descripción: Actualiza los datos de un recurso en la base de datos.
 *
 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
 * Fecha: Marzo 2025
 *
 * Parámetros de entrada: 
 *   - recurso_id 
 *   - recurso_codigo 
 *   - recurso_nombre
 *   - recurso_precio
 *   - recurso_aula
 *   - recurso_estado
 *
 * Salida: Notificaciones de éxito o error
 */

require_once "main.php";

// Almacenando id
$id = limpiar_cadena($_POST['recurso_id']);

// Verificando recurso
$check_recurso = conexion();
$check_recurso = $check_recurso->query("SELECT * FROM t_recurso WHERE recurso_id='$id'");

if ($check_recurso->rowCount() <= 0) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El recurso no existe en el sistema
        </div>
    ';
    exit();
} else {
    $datos = $check_recurso->fetch();
}
$check_recurso = null;

// Almacenando datos
$codigo = limpiar_cadena($_POST['recurso_codigo']);
$nombre = limpiar_cadena($_POST['recurso_nombre']);
$precio = limpiar_cadena($_POST['recurso_precio']);
$aula = limpiar_cadena($_POST['recurso_aula']);
$estado = limpiar_cadena($_POST['recurso_estado']);

// Verificando campos obligatorios
if ($codigo == "" || $nombre == "" || $precio == "" || $aula == "" || $estado == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    exit();
}

// Verificando integridad de los datos
if (verificar_datos("[a-zA-Z0-9- ]{1,70}", $codigo)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El CODIGO de BARRAS no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if (verificar_datos("^[0-9]+(\.[0-9]{1,2})?$", $precio)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El PRECIO no coincide con el formato solicitado
        </div>
    ';
    exit();
} else {
    $precio = str_replace(",", ".", $precio);
}

// Verificando codigo de recurso
if ($codigo != $datos['recurso_codigo']) {
    $check_codigo = conexion();
    $check_codigo = $check_codigo->query("SELECT recurso_codigo FROM t_recurso WHERE recurso_codigo='$codigo'");
    if ($check_codigo->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El CODIGO de BARRAS ingresado ya se encuentra registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_codigo = null;
}

// Verificando aula
if ($aula != $datos['aula_id']) {
    $check_aula = conexion();
    $check_aula = $check_aula->query("SELECT aula_id FROM t_aula WHERE aula_id='$aula'");
    if ($check_aula->rowCount() <= 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El aula seleccionada no existe
            </div>
        ';
        exit();
    }
    $check_aula = null;
}

// Verificando estado
if ($estado != $datos['estado_id']) {
    $check_estado = conexion();
    $check_estado = $check_estado->query("SELECT estado_id FROM t_estado WHERE estado_id='$estado'");
    if ($check_estado->rowCount() <= 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El estado seleccionado no existe
            </div>
        ';
        exit();
    }
    $check_estado = null;
}

// Actualizando datos
$actualizar_recurso = conexion();
$actualizar_recurso = $actualizar_recurso->prepare("UPDATE t_recurso SET recurso_codigo=:codigo,recurso_nombre=:nombre,recurso_precio=:precio,aula_id=:aula,estado_id=:estado WHERE recurso_id=:id");

$marcadores = [
    ":codigo" => $codigo,
    ":nombre" => $nombre,
    ":precio" => $precio,
    ":aula" => $aula,
    ":estado" => $estado,
    ":id" => $id
];

if ($actualizar_recurso->execute($marcadores)) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡recurso ACTUALIZADO!</strong><br>
            El recurso se actualizo con exito
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo actualizar el recurso, por favor intente nuevamente
        </div>
    ';
}
$actualizar_recurso = null;