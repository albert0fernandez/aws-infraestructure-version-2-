<?php
    /**
    * Archivo recurso_cargar_masivo.php
    * 
    * Este archivo se encarga de gestionar la carga masiva de recursos en el sistema de inventario mediante un archivo XML.
    * 
    * @autor RetaCantabria - ASIR1 -- Dpto. Informática - IES Alisal 
    * @fecha marzo de 2025
    * 
    * Parámetros de entrada:
    *    - xml_recursos: Archivo XML que contiene los datos de los recursos a cargar.
    * 
    * Salida:
    *    - Mensajes de éxito o error dependiendo del resultado de la operación de carga masiva.
     */

    require_once "main.php";

    $root_path = $_SERVER['DOCUMENT_ROOT'] . '/IESAlisal24RetaCantabriaASIR1-Solucion/INVENTARIO-main/';
    $ruta_xml= 'xml/recursos/';

    // Directorio de subida de recursos
    $upload_dir = $root_path.$ruta_xml;    

    // Comprobando si se ha seleccionado un xml 
    if(!($_FILES['xml_recursos']['name']!="" && $_FILES['xml_recursos']['size']>0 &&
            $_FILES['xml_recursos']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['xml_recursos']['tmp_name']))) 
    {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se ha seleccionado ningún archivo XML o el archivo es inválido.
            </div>
        ';
    } else {        
        $xml_tmp_file = $_FILES['xml_recursos']['tmp_name'];
        $xml_file = nombrar_xml("recursos-id-") . '.xml';
        $xml_dest_file = $upload_dir . basename($xml_file);

        // Comprobar si existe el directorio para guardar los ficheros de carga masiva y, si no, crearlo
        if(!file_exists($upload_dir)){
            crear_directorio($upload_dir);
        }

        // Comprobando formato del fichero subido
        if(mime_content_type($xml_tmp_file)!="application/xml" && mime_content_type($xml_tmp_file)!="text/xml"){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El fichero que ha seleccionado es de un formato que no está permitido
                </div>
            ';
            exit();
        }

        // Comprobando que el xml no supere el peso permitido
        if(($_FILES['xml_recursos']['size']/1024)>512){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El fichero que ha seleccionado supera el límite de tamaño permitido
                </div>
            ';
            exit();
        }

        // Moviendo el archivo subido al directorio de destino
        if (!move_uploaded_file($xml_tmp_file, $xml_dest_file)) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo mover el archivo subido al directorio de destino.
                </div>
            ';
            exit();
        }

        // Validando el archivo XML contra el esquema XSD
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        if (!$dom->load($xml_dest_file)) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        Error al cargar el archivo XML: ' . htmlspecialchars($error->message) . '
                    </div>
                ';
            }
            libxml_clear_errors();
            exit();
        }

        // Verificar si el documento tiene un elemento raíz
        if ($dom->documentElement === null) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El archivo XML no tiene un elemento raíz válido.
                </div>
            ';
            exit();
        }

        if (!$dom->schemaValidate('../schemas/validacion_recursos.xsd')) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El archivo XML no es válido según el esquema XSD dado.
                </div>
            ';
            exit();
        }
        
        // Cargar el archivo XML
        $xml = simplexml_load_file($xml_dest_file);
        // Comprobar si se ha cargado correctamente el archivo XML
        if ($xml === false) {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo cargar el archivo XML.
                </div>
            ';
            exit();
        }
    
        // Recorrer el fichero XML e insertar los datos en la base de datos
        foreach ($xml->recurso as $recurso) {
            $codigo = limpiar_cadena($recurso->recurso_codigo);
            $nombre = limpiar_cadena($recurso->recurso_nombre);
            $precio = limpiar_cadena($recurso->recurso_precio);
            $aula = limpiar_cadena($recurso->recurso_aula);
            $estado = limpiar_cadena($recurso->recurso_estado);

        
            // Comprobar si el recurso ya existe en la base de datos
            

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
            // Guardando datos
            $guardar_recurso = conexion();
            $guardar_recurso = $guardar_recurso->prepare("INSERT INTO t_recurso(recurso_codigo,recurso_nombre,estado_id,aula_id,recurso_precio) VALUES(:codigo,:nombre,:estado,:aula,:precio)");

            $marcadores = [
                ":codigo" => $codigo,
                ":nombre" => $nombre,
                ":estado" => $estado,
                ":aula" => $aula,
                ":precio" => $precio,
            ];

            // Ejecutamos la consulta
            $guardar_recurso->execute($marcadores);

            // Liberamos la memoria
            $guardar_recurso = null;
        }
        // Informamos al usuario de que el proceso ha finalizado con éxito    
        echo '
            <div class="notification is-success is-light">
                <strong>¡Carga masiva completada con éxito!</strong><br>
                Los datos del archivo XML se han guardado correctamente en la base de datos.
            </div>
        ';
    }
