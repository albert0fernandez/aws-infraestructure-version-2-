<?php
    /**
     * Nombre del fichero: proveedor_guardar.php
     * Descripción: Guarda los datos de un proveedor en la base de datos.
     *
     * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
     * Fecha: Marzo 2025
     *
     * Parámetros de entrada: 
     *   - proveedor_nombre: Nombre del proveedor (string)
     *   - proveedor_telefono: Teléfono del proveedor (string)
     *   - proveedor_email: Email del proveedor (string)
     * 
     * Parámetros de salida: Ninguno
     */

require_once "main.php";

// Almacenando datos
$nombre = limpiar_cadena($_POST['proveedor_nombre']);
$telefono = limpiar_cadena($_POST['proveedor_telefono']);
$email = limpiar_cadena($_POST['proveedor_email']);

// Verificando campos obligatorios
if ($nombre == "" || $telefono == "" || $email == "") {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No has llenado todos los campos que son obligatorios
        </div>
    ';
    exit();
}

// Verificando integridad de los datos
if (verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,30}", $nombre)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El NOMBRE no coincide con el formato solicitado
        </div>
    ';
    exit();
}

if ($telefono!= "") {
    if (verificar_datos("[0-9]{9}", $telefono)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La TELEFONO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
}
if ($email != "") {
    if (verificar_datos("[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}", $email)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El EMAIL no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
}

// Verificando nombre
$check_nombre = conexion();
$check_nombre = $check_nombre->query("SELECT proveedor_nombre FROM t_proveedor WHERE proveedor_nombre='$nombre'");
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
$guardar_proveedor = conexion();
$guardar_proveedor = $guardar_proveedor->prepare("INSERT INTO t_proveedor(proveedor_nombre, proveedor_telefono, proveedor_email) VALUES(:nombre, :telefono, :email)");

$marcadores = [
    ":nombre" => $nombre,
    ":telefono" => $telefono,
    ":email" => $email
];

$guardar_proveedor->execute($marcadores);

if ($guardar_proveedor->rowCount() == 1) {
    echo '
        <div class="notification is-info is-light">
            <strong>¡proveedor REGISTRADO!</strong><br>
            El proveedor se registro con exito
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            No se pudo registrar el proveedor, por favor intente nuevamente
        </div>
    ';
}
$guardar_proveedor = null;
