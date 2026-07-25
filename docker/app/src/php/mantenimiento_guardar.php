<?php
    /*
    * Nombre del fichero: mantenimiento_guardar.php
    * Descripción: Guarda el historial mantenimientos realizados a cada recurso.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada:
    *   - mantenimiento_recurso: ID del recurso.
    *   - mantenimiento_usuario: ID del usuario.
    *   - mantenimiento_fecha: Fecha del mantenimiento.
    *   - mantenimiento_descripción: Descripción del mantenimiento.
    *
    * Salida:
    *   - Mensaje de éxito o error en formato HTML.
    */

    
    require_once "main.php";

    // Almacenando datos
    $recurso=limpiar_cadena($_POST['mantenimiento_recurso']);
    $usuario=limpiar_cadena($_POST['mantenimiento_usuario']);
    $fecha=limpiar_cadena($_POST['mantenimiento_fecha']);
    $descripcion=limpiar_cadena($_POST['mantenimiento_descripcion']);

    // Verificando campos obligatorios
    if($recurso=="" || $usuario=="" || $fecha=="" || $descripcion==""){
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

    // Formatear fechas para MySQL
    $fecha_inicio = date('Y-m-d H:i:s', strtotime($mantenimiento_fecha));

    // Guardando datos
    $guardar_mantenimiento=conexion();
    $guardar_mantenimiento=$guardar_mantenimiento->prepare("INSERT INTO t_mantenimiento (recurso_id,usuario_id,mantenimiento_fecha, mantenimiento_descripcion) VALUES(:recurso,:usuario,:fecha,:descripcion)");

    $marcadores=[
        ":recurso"=>$recurso,
        ":usuario"=>$usuario,
        ":fecha"=>$fecha,
        ":descripcion"=>$descripcion
    ];

    $guardar_mantenimiento->execute($marcadores);

    if($guardar_mantenimiento->rowCount()==1){
        echo '
            <div class="notification is-info is-light">
                <strong>¡MANTENIMIENTO REGISTRADO!</strong><br>
                El dato se registro con exito
            </div>
        ';
    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo registrar el dato, por favor intente nuevamente
            </div>
        ';
    }
    $guardar_mantenimiento=null;
?>