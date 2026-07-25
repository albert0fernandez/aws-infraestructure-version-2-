<?php
    /**
     * Nombre del fichero: historial_uso_actualizar.php
     * Descripción: Actualiza el historial de uso de un recurso en la base de datos.
     *
     * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
     * Fecha: Marzo 2025
     *
     * Parámetros de entrada:
     *   - uso_id: ID del uso
     *   - recurso: ID del recurso
     *   - usuario: ID del usuario
     *   - uso_fecha_inicio: Fecha de inicio del uso
     *   - uso_fecha_fin: Fecha de fin del uso
     *
     *  Salida:
     *   - Notificación de éxito o error
     */

    require_once "main.php";

    // Almacenando id
    $id = limpiar_cadena($_POST['uso_id']);

    // Verificando registro
    $check_uso = conexion();
    $check_uso = $check_uso->query("SELECT * FROM t_historial_uso WHERE uso_id='$id'");

    if ($check_uso->rowCount() <= 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El registro no existe en el sistema
            </div>
        ';
        exit();
    } else {
        $datos = $check_uso->fetch();
    }
    $check_uso = null;

    // Almacenando datos
    $uso_id = limpiar_cadena($_POST['uso_id']);
    $recurso = limpiar_cadena($_POST['recurso']);
    $usuario = limpiar_cadena($_POST['usuario']);
    $fecha_inicio = limpiar_cadena($_POST['uso_fecha_inicio']);
    $fecha_fin = limpiar_cadena($_POST['uso_fecha_fin']);

    // Verificando campos obligatorios
    if ($uso_id == "" || $recurso == "" || $usuario == "" || $fecha_inicio == "") {
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

    // Verificar integridad de las fechas
    $resultado_verificacion = verificar_fechas($fecha_inicio, $fecha_fin);
    if ($resultado_verificacion !== true) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                ' . $resultado_verificacion . '
            </div>
        ';
        exit();
    }

    // Formatear fechas para MySQL
    $fecha_inicio = date('Y-m-d H:i:s', strtotime($fecha_inicio));
    $fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_fin));

    // Actualizando datos
    $actualizar_historial_uso = conexion();
    $actualizar_historial_uso = $actualizar_historial_uso->prepare("UPDATE t_historial_uso SET recurso_id=:recurso, usuario_id=:usuario, uso_fecha_inicio=:fecha_inicio,uso_fecha_fin=:fecha_fin WHERE t_historial_uso.uso_id=:id");

    $marcadores = [
        ":recurso" => $recurso,
        ":usuario" => $usuario,
        ":fecha_inicio" => $fecha_inicio,
        ":fecha_fin" => $fecha_fin,
        ":id" => $id
    ];

    if ($actualizar_historial_uso->execute($marcadores)) {
        echo '
            <div class="notification is-info is-light">
                <strong>¡recurso ACTUALIZADO!</strong><br>
                El dato se actualizo con exito
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo actualizar el dato de uso, por favor intente nuevamente
            </div>
        ';
    }
    $actualizar_historial_uso = null;