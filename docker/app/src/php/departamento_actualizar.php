<?php
    /*
    * Nombre del fichero: departamento_actualizar.php
    * Descripción: Actualiza los datos de un departamento en la base de datos.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada:
    *   - departamento_id: ID del departamento a actualizar.
    *   - departamento_nombre: Nuevo nombre del departamento.
    *   - departamento_ubicacion: Nueva ubicación del departamento.
    *   - departamento_descripcion: Nueva descripción del departamento.
    *
    * Salida:
    *   - Mensaje de éxito o error en la actualización del departamento.
    */

	require_once "main.php";

	// Almacenando id
    $id=limpiar_cadena($_POST['departamento_id']);

    // Verificando departamento
	$check_departamento=conexion();
	$check_departamento=$check_departamento->query("SELECT * FROM t_departamento WHERE departamento_id='$id'");

    if($check_departamento->rowCount()<=0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El departamento no existe en el sistema
            </div>
        ';
        exit();
    }else{
        $datos=$check_departamento->fetch();
    }
    $check_departamento=null;

    // Almacenando datos
    $nombre=limpiar_cadena($_POST['departamento_nombre']);
    $ubicacion=limpiar_cadena($_POST['departamento_ubicacion']);
    $responsable=limpiar_cadena($_POST['departamento_usuario']);

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
    if(verificar_datos("[A-Z][a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,24}",$nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if($ubicacion!=""){
        if(verificar_datos("E[0-4]-P[0-3]",$ubicacion)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La UBICACION no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    }


    // Verificando nombre
    if($nombre!=$datos['departamento_nombre']){
        $check_nombre=conexion();
        $check_nombre=$check_nombre->query("SELECT departamento_nombre FROM t_departamento WHERE departamento_nombre='$nombre'");
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
    $actualizar_departamento=conexion();
    $actualizar_departamento=$actualizar_departamento->prepare("UPDATE t_departamento SET departamento_nombre=:nombre,departamento_ubicacion=:ubicacion, responsable_id=:responsable WHERE departamento_id='$id'");

    $marcadores=[
        ":nombre"=>$nombre,
        ":ubicacion"=>$ubicacion,
        ":responsable"=>$responsable
    ];

    if($actualizar_departamento->execute($marcadores)){
        echo '
            <div class="notification is-info is-light">
                <strong>¡departamento ACTUALIZADO!</strong><br>
                El departamento se actualizo con exito
            </div>
        ';
    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo actualizar el departamento, por favor intente nuevamente
            </div>
        ';
    }
    $actualizar_departamento=null;
?>