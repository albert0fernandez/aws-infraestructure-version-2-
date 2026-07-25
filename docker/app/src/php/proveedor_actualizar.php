<!--
    *
    * Nombre del fichero: proveedor_actualizar.php
    * Descripción: Actualiza los datos de un proveedor en la base de datos.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada:
    *   - proveedor_id: ID del proveedor a actualizar.
    *   - proveedor_nombre: Nuevo nombre del proveedor.
    *   - proveedor_telefono: Nuevo teléfono del proveedor.
    *   - proveedor_email: Nuevo correo electrónico del proveedor.
    *
    *
    * Salida:
    *   - Mensaje de éxito o error en la actualización del proveedor.
-->

<?php
	require_once "main.php";

	// Almacenando id
    $id=limpiar_cadena($_POST['proveedor_id']);

    // Verificando proveedor
	$check_proveedor=conexion();
	$check_proveedor=$check_proveedor->query("SELECT * FROM t_proveedor WHERE proveedor_id='$id'");

    if($check_proveedor->rowCount()<=0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El proveedor no existe en el sistema
            </div>
        ';
        exit();
    }else{
        $datos=$check_proveedor->fetch();
    }
    $check_proveedor=null;

    // Almacenando datos
    $nombre=limpiar_cadena($_POST['proveedor_nombre']);
    $telefono=limpiar_cadena($_POST['proveedor_telefono']);
    $email=limpiar_cadena($_POST['proveedor_email']);

    // Verificando campos obligatorios
    if($nombre=="" || $telefono=="" || $email==""){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    // Verificando integridad de los datos
    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]{3,30}",$nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[0-9]{9}",$telefono)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El TELEFONO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if($email!=""){
        if(verificar_datos("[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}",$email)){
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
    if($nombre!=$datos['proveedor_nombre']){
        $check_nombre=conexion();
        $check_nombre=$check_nombre->query("SELECT proveedor_nombre FROM t_proveedor WHERE proveedor_nombre='$nombre'");
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
    $actualizar_proveedor=conexion();
    $actualizar_proveedor=$actualizar_proveedor->prepare("UPDATE t_proveedor SET proveedor_nombre=:nombre, proveedor_telefono=:telefono, proveedor_email=:email WHERE proveedor_id=:id");

    $marcadores=[
        ":nombre"=>$nombre,
        ":telefono"=>$telefono,
        ":email"=>$email,
        ":id"=>$id
    ];

    if($actualizar_proveedor->execute($marcadores)){
        echo '
            <div class="notification is-info is-light">
                <strong>¡proveedor ACTUALIZADO!</strong><br>
                El proveedor se actualizo con exito
            </div>
        ';
    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo actualizar el proveedor, por favor intente nuevamente
            </div>
        ';
    }
    $actualizar_proveedor=null;
?>