<?php
    /*
    * Nombre del fichero: historial_uso_guardar.php
    * Descripción: Guarda el historial de uso de un recurso por un usuario.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada:
    *   - historial_uso_recurso: ID del recurso.
    *   - historial_uso_usuario: ID del usuario.
    *   - uso_fecha_inicio: Fecha y hora de inicio del uso.
    *   - uso_fecha_fin: Fecha y hora de fin del uso.
    *
    * Salida:
    *   - Mensaje de éxito o error en formato HTML.
    */

    
    require_once "main.php";

    // Almacenando datos
    $recurso=limpiar_cadena($_POST['historial_uso_recurso']);
    $usuario=limpiar_cadena($_POST['historial_uso_usuario']);
    $fecha_inicio=limpiar_cadena($_POST['uso_fecha_inicio']);
    $fecha_fin=limpiar_cadena($_POST['uso_fecha_fin']);

    // Verificando campos obligatorios
    if($recurso=="" || $usuario=="" || $fecha_inicio=="" || $fecha_fin==""){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    // Verificando recurso
    $check_recurso=conexion();
    $check_recurso=$check_recurso->query("SELECT recurso_id FROM t_recurso WHERE recurso_id='$recurso'");
    if($check_recurso->rowCount()<=0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El RECURSO ingresado no encuentra registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_recurso=null;

    // Verificando usuario
    $check_usuario=conexion();
    $check_usuario=$check_usuario->query("SELECT usuario_id FROM t_usuario WHERE usuario_id='$usuario'");
    if($check_usuario->rowCount()<=0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El USUARIO no está registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_usuario=null;

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

    // Guardando datos
    $guardar_historial_uso=conexion();
    $guardar_historial_uso=$guardar_historial_uso->prepare("INSERT INTO t_historial_uso (recurso_id,usuario_id,uso_fecha_inicio,uso_fecha_fin) VALUES(:recurso,:usuario,:fecha_inicio,:fecha_fin)");

    $marcadores=[
        ":recurso"=>$recurso,
        ":usuario"=>$usuario,
        ":fecha_inicio"=>$fecha_inicio,
        ":fecha_fin"=>$fecha_fin
    ];

    $guardar_historial_uso->execute($marcadores);

    if($guardar_historial_uso->rowCount()==1){
        echo '
            <div class="notification is-info is-light">
                <strong>¡USUARIO REGISTRADO!</strong><br>
                El dato se registro con exito
            </div>
        ';
    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo registrar el datoo, por favor intente nuevamente
            </div>
        ';
    }
    $guardar_historial_uso=null;
?>