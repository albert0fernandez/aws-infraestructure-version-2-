<?php
    /**
     * Nombre del fichero: recurso_guardar.php
     * Descripción: Este script guarda un recurso en la base de datos.
     *
     * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
     * Fecha: Marzo 2025
     *
     * Parámetros de entrada: 
     *    - recurso_codigo
     *    - recurso_nombre
     *    - recurso_precio 
     *    - recurso_aula 
     *    - recurso_estado 
     *    - recurso_foto
     * Parámetros de salida: 
     *    - Notificaciones de éxito o error
     */

    require_once "../inc/session_start.php";
    require_once "main.php";

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
    if (verificar_datos("[A-Z]{3}-[0-9]{4}", $codigo)) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El CODIGO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,50}", $nombre)) {
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

    // Comprobamos que el código no ha sido registrado previamente
    $check_codigo = conexion();
    $check_codigo = $check_codigo->query("SELECT recurso_codigo FROM t_recurso WHERE recurso_codigo='$codigo'");
    if ($check_codigo->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El CODIGO ingresado ya se encuentra registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_codigo = null;


    // Comprobamos que el estado seleccionado existe
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

    // Comprobamos que el aula seleccionada existe
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

    // Directorios de imagenes
    $img_dir = '../img/recurso/';

    // Comprobando si se ha seleccionado una imagen
    if ($_FILES['recurso_foto']['name'] != "" && $_FILES['recurso_foto']['size'] > 0) {

        // Comprobar si existe el directorio para guardar imágenes y crearlo de no ser así
        crear_directorio($img_dir);

        // Comprobando formato de las imagenes
        if (mime_content_type($_FILES['recurso_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['recurso_foto']['tmp_name']) != "image/png") {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La imagen que ha seleccionado es de un formato que no está permitido
                </div>
            ';
            exit();
        }

        // Comprobando que la imagen no supere el peso permitido
        if (($_FILES['recurso_foto']['size'] / 1024) > 3072) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La imagen que ha seleccionado supera el límite de peso permitido
                </div>
            ';
            exit();
        }

        // extension de las imagenes
        switch (mime_content_type($_FILES['recurso_foto']['tmp_name'])) {
            case 'image/jpeg':
                $img_ext = ".jpg";
                break;
            case 'image/png':
                $img_ext = ".png";
                break;
        }

        // Cambiando permisos al directorio
        chmod($img_dir, 0777);

        // Nombre de la imagen
        $img_nombre = renombrar_fotos($nombre);

        // Nombre final de la imagen
        $foto = $img_nombre . $img_ext;

        // Moviendo imagen al directorio
        if (!move_uploaded_file($_FILES['recurso_foto']['tmp_name'], $img_dir . $foto)) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No podemos subir la imagen al sistema en este momento, por favor inténtelo de nuevo.
                </div>
            ';
            exit();
        }

    } else {
        $foto = "";
    }

    // Guardando datos
    $guardar_recurso = conexion();
    $guardar_recurso = $guardar_recurso->prepare("INSERT INTO t_recurso(recurso_codigo,recurso_nombre,estado_id,aula_id,recurso_precio,recurso_fecha_compra,recurso_fin_garantia,recurso_foto) VALUES(:codigo,:nombre,:estado,:aula,:precio,:fecha_compra,:fin_garantia,:foto)");

    $marcadores = [
        ":codigo" => $codigo,
        ":nombre" => $nombre,
        ":estado" => $estado,
        ":aula" => $aula,
        ":precio" => $precio,
        ":fecha_compra" => date("Y-m-d"),
        ":fin_garantia" => date("Y-m-d", strtotime("+2 year")),
        ":foto" => $foto
    ];

    $guardar_recurso->execute($marcadores);

    if ($guardar_recurso->rowCount() == 1) {
        echo '
            <div class="notification is-info is-light">
                <strong>¡recurso REGISTRADO!</strong><br>
                El recurso se registro con exito
            </div>
        ';
    } else {

        if (is_file($img_dir . $foto)) {
            chmod($img_dir . $foto, 0777);
            unlink($img_dir . $foto);
        }

        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo registrar el recurso, por favor intente nuevamente
            </div>
        ';
    }
    $guardar_recurso = null;