<!--
    * Nombre del fichero: history_use_update.php
    * Descripción: Actualiza el registro de uso de recursos.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada: 
    *   - use_id_up: ID del uso a actualizar.
    *
    * Salida: Ninguno
-->

<div class="container is-fluid mb-6">
    <h1 class="title">Historial de uso de recursos</h1>
    <h2 class="subtitle">Actualizar registro</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        include "./inc/btn_back.php";

        require_once "./php/main.php";

        $id = (isset($_GET['use_id_up'])) ? $_GET['use_id_up'] : 0;
        $id=limpiar_cadena($id);

        // Verificando registro de uso
        $check_historial_uso=conexion();
        $check_historial_uso=$check_historial_uso->query("SELECT * FROM t_historial_uso WHERE t_historial_uso.uso_id='$id'");

        if($check_historial_uso->rowCount()>0){
            $datos=$check_historial_uso->fetch();

            // Calcular la fecha máxima permitida (fecha actual + 3 meses)
            $fechaActual = new DateTime();
            $fechaMaxima = $fechaActual->add(new DateInterval('P3M'))->format('Y-m-d\TH:i');
    ?>

    <div class="form-rest mb-6 mt-6"></div>

    <form action="./php/historial_uso_actualizar.php" method="POST" class="FormularioAjax" autocomplete="off" onsubmit="return validarFechas();">

		<input type="hidden" name="uso_id" value="<?php echo $datos['uso_id']; ?>" required >
			
        <div class="columns">
            <div class="column">
                <div class="control">
                    <label>Recurso</label><br />
                    <div class="select is-rounded">
                        <select name="recurso" >
                            <option value="" selected="" >Seleccione una opción</option>
                            <?php
                                $recursos=conexion();
                                $recursos=$recursos->query("SELECT * FROM t_recurso");
                                if($recursos->rowCount()>0){
                                    $recursos=$recursos->fetchAll();
                                    foreach($recursos as $row){
                                        if($datos['recurso_id']==$row['recurso_id']){
                                            echo '<option value="'.$row['recurso_id'].'" selected="" >'.$row['recurso_nombre'].' (Actual)</option>';
                                        }else{
                                            echo '<option value="'.$row['recurso_id'].'" >'.$row['recurso_nombre'].'</option>';
                                        }
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
                    <label>Usuario</label>
                    <br />
                    <div class="select is-rounded">
                        <select name="usuario" >
                            <option value="" selected="" >Seleccione una opción</option>
                            <?php
                                $usuarios=conexion();
                                $usuarios=$usuarios->query("SELECT * FROM t_usuario");
                                if($usuarios->rowCount()>0){
                                    $usuarios=$usuarios->fetchAll();
                                    foreach($usuarios as $row){
                                        if($datos['usuario_id']==$row['usuario_id']){ 
                                            echo '<option value="'.$row['usuario_id'].'" selected="" >'.$row['usuario_nombre'].' (Actual)</option>';    
                                        }else{
                                            echo '<option value="'.$row['usuario_id'].'" >'.$row['usuario_nombre'].'</option>';
                                        }
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
                    <input type="datetime-local" name="uso_fecha_inicio" min="2020-01-01T07:00" max="<?php echo $fechaActual->format('Y-m-d\TH:i'); ?>" value="<?php echo date('Y-m-d\TH:i', strtotime($datos['uso_fecha_inicio'])); ?>" required />
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label>Fecha y hora de fin</label><br>
                    <input type="datetime-local" name="uso_fecha_fin" min="2022-01-01T07:00" max="<?php echo $fechaActual->format('Y-m-d\TH:i'); ?>" value="<?php echo date('Y-m-d\TH:i', strtotime($datos['uso_fecha_fin'])); ?>" />
                </div>
            </div>
        </div> 
    
        <p class="has-text-centered">
            <button type="submit" class="button is-success is-rounded">Actualizar</button>
        </p>
    </form>

    <?php 
        }else{
            include "./inc/error_alert.php";
        }
        $check_recurso=null;
    ?>
</div>

<script src="../js/validar_fechas.js"></script>