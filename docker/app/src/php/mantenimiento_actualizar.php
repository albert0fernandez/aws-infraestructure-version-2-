<?php
    /**
     * Nombre del fichero: mantenimiento_actualizar.php
     * Descripción: Actualiza el mantenimiento de un recurso en la base de datos.
     *
     * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
     * Fecha: Marzo 2025
     *
     * Parámetros de entrada:
     *   - mantenimiento_id: ID del mantenimiento
     *   - recurso: ID del recurso
     *   - usuario: ID del usuario
     *   - mantenimiento_fecha: Fecha del mantenimiento
     *   - descripcion: Descripción del mantenimiento
     *
     *  Salida:
     *   - Notificación de éxito o error
     */

    require_once "main.php";

    // Almacenando id
    $id = limpiar_cadena($_POST['mantenimiento_id']);

    // Verificando registro
    $check_mantenimiento = conexion();
    $check_mantenimiento = $check_mantenimiento->query("SELECT * FROM t_mantenimiento WHERE mantenimiento_id='$id'");

    if ($check_mantenimiento->rowCount() <= 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El registro no existe en el sistema
            </div>
        ';
        exit();
    } else {
        $datos = $check_mantenimiento->fetch();
    }
    $check_mantenimiento = null;

    // Almacenando datos
    $mantenimiento_id = limpiar_cadena($_POST['mantenimiento_id']);
    $recurso = limpiar_cadena($_POST['recurso']);
    $usuario = limpiar_cadena($_POST['usuario']);
    $fecha = limpiar_cadena($_POST['mantenimiento_fecha']);
    $descripcion = limpiar_cadena($_POST['descripcion']);

    // Verificando campos obligatorios
    if ($mantenimiento_id == "" || $recurso == "" || $usuario == "" || $fecha == "" || $descripcion == "") {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    // Verificando recurso
    $check_recurso = conexion();
    $check_recurso = $check_recurso->query("SELECT recurso_id FROM t_recurso WHERE recurso_id='$recurso'");
    if ($check_recurso->rowCount() <= 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El RECURSO ingresado no encuentra registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_recurso = null;

    // Verificando usuario
    $check_usuario = conexion();
    $check_usuario = $check_usuario->query("SELECT usuario_id FROM t_usuario WHERE usuario_id='$usuario'");
    if ($check_usuario->rowCount() <= 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El USUARIO no está registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_usuario = null;

    // Formatear fechas para MySQL
    $fecha = date('Y-m-d H:i:s', strtotime($fecha));

    // Actualizando datos
    $actualizar_mantenimiento = conexion();
    $actualizar_mantenimiento = $actualizar_mantenimiento->prepare("UPDATE t_mantenimiento SET recurso_id=:recurso, usuario_id=:usuario, mantenimiento_fecha=:fecha,mantenimiento_descripcion=:descripcion WHERE t_mantenimiento.mantenimiento_id=:id");

    $marcadores = [
        ":recurso" => $recurso,
        ":usuario" => $usuario,
        ":fecha" => $fecha,
        ":descripcion" => $descripcion,
        ":id" => $id
    ];

    if ($actualizar_mantenimiento->execute($marcadores)) {
        echo '
            <div class="notification is-info is-light">
                <strong>¡Mantenimiento ACTUALIZADO!</strong><br>
                El dato se actualizo con exito
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo actualizar el dato de mantenimiento, por favor intente nuevamente
            </div>
        ';
    }
    $actualizar_mantenimiento = null;