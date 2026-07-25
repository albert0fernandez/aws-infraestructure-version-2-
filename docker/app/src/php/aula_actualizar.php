<?php
    /*
    * Nombre del fichero: aula_actualizar.php
    * Descripción: Actualiza los datos de un aula en la base de datos.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada:
    *   - aula_id: ID del aula a actualizar.
    *   - aula_nombre: Nuevo nombre del aula.
    *   - aula_ubicacion: Nueva ubicación del aula.
    *   - aula_descripcion: Nueva descripción del aula.
    *
    * Salida:
    *   - Mensaje de éxito o error en la actualización del aula.
    */

	require_once "main.php";

	// Almacenando id
    $id=limpiar_cadena($_POST['aula_id']);

    // Verificando aula
	$check_aula=conexion();
	$check_aula=$check_aula->query("SELECT * FROM t_aula WHERE aula_id='$id'");

    if($check_aula->rowCount()<=0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El aula no existe en el sistema
            </div>
        ';
        exit();
    }else{
        $datos=$check_aula->fetch();
    }
    $check_aula=null;

    // Almacenando datos
    $nombre=limpiar_cadena($_POST['aula_nombre']);
    $ubicacion=limpiar_cadena($_POST['aula_ubicacion']);
    $descripcion=limpiar_cadena($_POST['aula_descripcion']);

    // Verificando campos obligatorios
    if($nombre==""){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    // Verificando integridad de los datos
    if(verificar_datos("A[0-9]{3}",$nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if($ubicacion!=""){
        if(verificar_datos("E[1-4]-P[0-3]",$ubicacion)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La UBICACION no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    }

    if($descripcion!=""){
        if(verificar_datos("[A-Z][a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,24}",$descripcion)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La DESCRIPCION no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    }

    // Verificando nombre
    if($nombre!=$datos['aula_nombre']){
        $check_nombre=conexion();
        $check_nombre=$check_nombre->query("SELECT aula_nombre FROM t_aula WHERE aula_nombre='$nombre'");
        if($check_nombre->rowCount()>0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
                </div>
            ';
            exit();
        }
        $check_nombre=null;
    }

    // Actualizar datos
    $actualizar_aula=conexion();
    $actualizar_aula=$actualizar_aula->prepare("UPDATE t_aula SET aula_nombre=:nombre,aula_ubicacion=:ubicacion, aula_descripcion=:descripcion WHERE aula_id=:id");

    $marcadores=[
        ":nombre"=>$nombre,
        ":ubicacion"=>$ubicacion,
        ":descripcion"=>$descripcion,
        ":id"=>$id
    ];

    if($actualizar_aula->execute($marcadores)){
        echo '
            <div class="notification is-info is-light">
                <strong>¡aula ACTUALIZADA!</strong><br>
                El aula se actualizo con exito
            </div>
        ';
    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo actualizar el aula, por favor intente nuevamente
            </div>
        ';
    }
    $actualizar_aula=null;
?>