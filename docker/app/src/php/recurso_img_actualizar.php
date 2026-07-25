<?php
    /**
     * Nombre del fichero: recurso_img_actualizar.php
     * Descripción: Actualiza la imagen de un recurso en la base de datos.
     * 
     * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
     * Fecha: Marzo 2025
     * 
     * Parámetros de entrada: 
     *   - img_up_id: ID del recurso a actualizar (POST)
     *   - recurso_foto: Archivo de imagen a subir (FILES)
     * 
     * Salida: 
     *   - Mensaje de éxito o error en formato HTML
     */

    require_once "main.php";

    // Almacenando datos
    $resource_id=limpiar_cadena($_POST['img_up_id']);

    // Verificando recurso
    $check_recurso=conexion();
    $check_recurso=$check_recurso->query("SELECT * FROM t_recurso WHERE recurso_id='$resource_id'");

    if($check_recurso->rowCount()==1){
        $datos=$check_recurso->fetch();
    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen del recurso que intenta actualizar no existe
            </div>
        ';
        exit();
    }
    $check_recurso=null;

    // Comprobando si se ha seleccionado una imagen
    if($_FILES['recurso_foto']['name']=="" || $_FILES['recurso_foto']['size']==0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No ha seleccionado ninguna imagen o foto
            </div>
        ';
        exit();
    }

    // Directorios de imagenes
    $img_dir='../img/recurso/';

    // Comprobar si existe el directorio para guardar imágenes y crearlo de no ser así
    crear_directorio($img_dir);

    // Cambiando permisos al directorio
    chmod($img_dir, 0777);

    // Comprobando formato de las imagenes
    if(mime_content_type($_FILES['recurso_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['recurso_foto']['tmp_name'])!="image/png"){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen que ha seleccionado es de un formato que no está permitido
            </div>
        ';
        exit();
    }

    // Comprobando que la imagen no supere el peso permitido
    if(($_FILES['recurso_foto']['size']/1024)>3072){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen que ha seleccionado supera el límite de peso permitido
            </div>
        ';
        exit();
    }

    // Extensión de las imagenes
    switch(mime_content_type($_FILES['recurso_foto']['tmp_name'])){
        case 'image/jpeg':
            $img_ext=".jpg";
        break;
        case 'image/png':
            $img_ext=".png";
        break;
    }

    // Nombre de la imagen
    $img_nombre=renombrar_fotos($datos['recurso_nombre']);

    // Nombre final de la imagen
    $foto=$img_nombre.$img_ext;

    // Moviendo imagen al directorio
    if(!move_uploaded_file($_FILES['recurso_foto']['tmp_name'], $img_dir.$foto)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No podemos subir la imagen al sistema en este momento, por favor intente nuevamente
            </div>
        ';
        exit();
    }

    // Eliminando la imagen anterior
    if(is_file($img_dir.$datos['recurso_foto']) && $datos['recurso_foto']!=$foto){
        chmod($img_dir.$datos['recurso_foto'], 0777);
        unlink($img_dir.$datos['recurso_foto']);
    }

    // Actualizando datos
    $actualizar_recurso=conexion();
    $actualizar_recurso=$actualizar_recurso->prepare("UPDATE t_recurso SET recurso_foto=:foto WHERE recurso_id=:id");

    $marcadores=[
        ":foto"=>$foto,
        ":id"=>$resource_id
    ];

    if($actualizar_recurso->execute($marcadores)){
        echo '
            <div class="notification is-info is-light">
                <strong>¡IMAGEN O FOTO ACTUALIZADA!</strong><br>
                La imagen del recurso ha sido actualizada exitosamente, pulse Aceptar para recargar los cambios.

                <p class="has-text-centered pt-5 pb-5">
                    <a href="index.php?vista=resource_img&resource_id_up='.$resource_id.'" class="button is-link is-rounded">Aceptar</a>
                </p">
            </div>
        ';
    }else{

        if(is_file($img_dir.$foto)){
            chmod($img_dir.$foto, 0777);
            unlink($img_dir.$foto);
        }

        echo '
            <div class="notification is-warning is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No podemos subir la imagen al sistema en este momento, por favor intente nuevamente
            </div>
        ';
    }
    $actualizar_recurso=null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Masiva de Aulas</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .header, .footer {
            background-color: #3273dc;
            padding: 1rem;
            color: #fff;
            text-align: center;
        }
        .header h1, .footer p {
            margin: 0;
        }
        .form-title {
            color: #3273dc;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .button.is-primary {
            background-color: #3273dc;
            border-color: #3273dc;
        }
        .button.is-primary:hover {
            background-color: #275ab5;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Carga Masiva</h1>
    </div>

    <section class="section">
        <div class="container">
            <h2 class="form-title">Carga Masiva</h2>
            <form action="aula_carga_masiva.php" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label class="label">Seleccionar archivo</label>
                    <div class="control has-icons-left">
                        <input class="input" type="file" name="csv_file" accept=".csv" required>
                        <span class="icon is-small is-left">
                            <i class="fas fa-file-csv"></i>
                        </span>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-primary is-fullwidth" type="submit">Cargar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div class="footer">
        <p>&copy; 2025 RetaCantabria - ASIR1 - IES Alisal</p>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>