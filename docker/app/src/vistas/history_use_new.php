<!--
    * Nombre del fichero: history_use_new.php
    * Descripción: Formulario para registrar un nuevo historial de uso.
    * 
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    * 
    * Parámetros de entrada: 
    *   - historial_uso_recurso: ID del recurso seleccionado.
    *   - historial_uso_usuario: ID del usuario seleccionado.
    *   - uso_fecha_inicio: Fecha y hora de inicio del uso.
    *   - uso_fecha_fin: Fecha y hora de fin del uso.
    * 
    *   Salida: Ninguno
-->

<div class="container is-fluid mb-6">
    <h1 class="title">Historial de uso de recursos</h1>
    <h2 class="subtitle">Nuevo registro</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        // Requiere el archivo main.php para funciones principales
        require_once "./php/main.php";
    ?>

    <div class="form-rest mb-6 mt-6"></div>

    <form action="./php/historial_uso_guardar.php" method="POST" 
        class="FormularioAjax" autocomplete="off" 
        enctype="multipart/form-data" onsubmit="return validarFechas()">
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Recurso</label><br>
                    <div class="select is-rounded">
                        <select name="historial_uso_recurso" >
                            <option value="" selected="" >Seleccione una opción</option>
                            <?php
                                // Conexión a la base de datos y obtención de recursos
                                $recursos=conexion();
                                $recursos=$recursos->query("SELECT * FROM t_recurso");
                                if($recursos->rowCount()>0){
                                    $recursos=$recursos->fetchAll();
                                    foreach($recursos as $row){
                                        echo '<option value="'.$row['recurso_id'].'" >'.$row['recurso_nombre'].'</option>';
                                    }
                                }
                                $recursos=null;
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Usuario</label><br>
                    <div class="select is-rounded">
                        <select name="historial_uso_usuario" >
                            <option value="" selected="" >Seleccione una opción</option>
                            <?php
                                // Conexión a la base de datos y obtención de usuarios
                                $usuarios=conexion();
                                $usuarios=$usuarios->query("SELECT * FROM t_usuario");
                                if($usuarios->rowCount()>0){
                                    $usuarios=$usuarios->fetchAll();
                                    foreach($usuarios as $row){
                                        echo '<option value="'.$row['usuario_id'].'" >'.$row['usuario_nombre']." ".$row['usuario_apellido'].'</option>';
                                    }
                                }
                                $usuarios=null;
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Fecha y hora de inicio</label><br>
                    <input type="datetime-local" name="uso_fecha_inicio" min="2022-01-01T07:00" max="<?php echo $currentDateTime ?>"  required />
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Fecha y hora de fin</label><br>
                    <input type="datetime-local" name="uso_fecha_fin"  min="2022-01-0107:00" max="<?php echo $currentDateTime ?>" />
                </div>
            </div>
        </div>
        
        <p class="has-text-centered">
            <button type="submit" class="button is-info is-rounded">Guardar</button>
        </p>
    </form>
</div>

<script src="../js/validar_fechas.js></script>