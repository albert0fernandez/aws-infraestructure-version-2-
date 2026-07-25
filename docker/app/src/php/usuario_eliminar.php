<?php
    /**
     * Nombre del fichero: usuario_eliminar.php
     * Descripción: Elimina un usuario de la base de datos si no tiene recursos asociados.
     *
     * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
     * Fecha: Marzo 2025
     * 
     * Parámetros de entrada: 
     *   - user_id_del: ID del usuario a eliminar.
     * 
     * Salida: 
     *   - Mensaje de éxito o error
     */

    require_once "main.php";

    // Almacenando datos
    $user_id_del = limpiar_cadena($_GET['user_id_del']);

    // Verificando usuario
    $check_usuario = conexion();
    $check_usuario = $check_usuario->query("SELECT usuario_id FROM t_usuario WHERE usuario_id='$user_id_del'");

    if ($check_usuario->rowCount() == 1) {

        // Comprobamos que no hay recursos asociados a este usuario 
        $check_historial_uso = conexion();
        $check_historial_uso = $check_historial_uso->query("SELECT usuario_id FROM t_historial_uso WHERE usuario_id='$user_id_del' LIMIT 1");

        $check_mantenimiento = conexion();
        $check_mantenimiento = $check_mantenimiento->query("SELECT usuario_id FROM t_mantenimiento WHERE usuario_id='$user_id_del' LIMIT 1");

        if ($check_historial_uso->rowCount() <= 0 && $check_mantenimiento->rowCount() <= 0) {

            $eliminar_usuario = conexion();
            $eliminar_usuario = $eliminar_usuario->prepare("DELETE FROM t_usuario WHERE usuario_id=:id");

            $eliminar_usuario->execute([":id" => $user_id_del]);

            if ($eliminar_usuario->rowCount() == 1) {
                echo '
                    <div class="notification is-info is-light">
                        <strong>¡USUARIO ELIMINADO!</strong><br>
                        Los datos del usuario se eliminaron con éxito.
                    </div>
                ';
            } else {
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrió un error inesperado!</strong><br>
                        No se pudo eliminar el usuario, por favor intente nuevamente.
                    </div>
                ';
            }
            $eliminar_usuario = null;
        } else {
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrió un error inesperado!</strong><br>
                    No podemos eliminar el usuario ya que tiene recursos asociados.
                </div>
            ';
        }
        $check_historial_uso = null;
        $check_mantenimiento = null;
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrió un error inesperado!</strong><br>
                El USUARIO que intenta eliminar no existe.
            </div>
        ';
    }
    $check_usuario = null;
?>