<?php
    /** 
    * Nombre del fichero: departamento_guardar.php
    * Descripción: Guarda un nuevo departamento en la base de datos después de validar los datos de entrada.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: [Fecha]
    *
    * Parámetros de entrada: 
    *   - departamento_nombre
    *   - departamento_ubicacion
    *   - responsable_Id
    *   
    *
    * Salida: 
    *   - Notificaciones de éxito o error
    */

    require_once "main.php";

    // Almacenando datos
    $nombre=limpiar_cadena($_POST['departamento_nombre']);
    $ubicacion=limpiar_cadena($_POST['departamento_ubicacion']);
    $responsable=limpiar_cadena($_POST['responsable_id']);
   

    // Verificando campos obligatorios
    if($nombre=="" || $ubicacion=="" || $responsable==""){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    // Verificando integridad de los datos
    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    // Verificando el formato (ejemplo: E1-P2, A10-B5)
    if(!verificar_datos("/^[A-Z][0-9]+-[A-Z][0-9]+$/", $ubicacion)) {
    echo '
        <div class="notification is-danger is-light">
            <strong>¡Error!</strong><br>
            El formato del código debe ser: LETRA-NÚMERO (ejemplo: E1-P2)
        </div>
    ';
    exit();
}

    if(verificar_datos("",$responsable)){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    
    // Verificando Ubicacion
    if($codigo != "") {
        // Expresión regular para validar el formato (ej: E1-P2, A10-B5)
        if(preg_match("/^[A-Z][0-9]+-[A-Z][0-9]+$/", $codigo)) {
            // Verificar si el código ya existe en la base de datos
            $check_codigo = conexion();
            $check_codigo = $check_codigo->query("SELECT departamento_ubicacion FROM t_departamento WHERE departamento_ubicacion = '$ubicacion'");
            
            if($check_codigo->rowCount() > 0) {
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrió un error inesperado!</strong><br>
                        El código ingresado ya está registrado, por favor elija otro.
                    </div>
                ';
                exit();
            }
            $check_codigo = null; // Cerrar conexión
        } else {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    El código debe tener el formato "LETRA-NÚMERO" (ejemplo: E1-P2).<br>
                    - Letras en MAYÚSCULAS.<br>
                    - Debe incluir un guión (-) en medio.
                </div>
            ';
            exit();
        } 
    }

    // Verificando Responsable
    $check_usuario=conexion();
    $check_usuario=$check_usuario->query("SELECT usuario_usuario FROM t_usuario WHERE usuario_usuario='$usuario'");
    if($check_usuario->rowCount()>0){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El USUARIO ingresado ya se encuentra registrado, por favor elija otro
            </div>
        ';
        exit();
    }
    $check_usuario=null;

    // Verificando claves
    if($clave_1!=$clave_2){
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                Las CLAVES que ha ingresado no coinciden
            </div>
        ';
        exit();
    }else{
        $clave=password_hash($clave_1,PASSWORD_BCRYPT,["cost"=>10]);
    }

    // Guardando datos
    $guardar_usuario=conexion();
    $guardar_usuario=$guardar_usuario->prepare("INSERT INTO t_usuario(usuario_nombre,usuario_apellido,usuario_usuario,usuario_clave,usuario_email) VALUES(:nombre,:apellido,:usuario,:clave,:email)");

    $marcadores=[
        ":nombre"=>$nombre,
        ":apellido"=>$apellido,
        ":usuario"=>$usuario,
        ":clave"=>$clave,
        ":email"=>$email
    ];

    $guardar_usuario->execute($marcadores);

    if($guardar_usuario->rowCount()==1){
        echo '
            <div class="notification is-info is-light">
                <strong>¡USUARIO REGISTRADO!</strong><br>
                El usuario se registro con exito
            </div>
        ';
    }else{
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo registrar el usuario, por favor intente nuevamente
            </div>
        ';
    }
    $guardar_usuario=null;
    ?>

