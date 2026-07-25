<!--
	* Nombre del fichero: resource_update.php
	* Descripción: Actualiza los datos de un recurso en la base de datos.
	*
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	*
	* Parámetros de entrada: resource_id_up (ID del recurso a actualizar)
	* Salida: Ninguno
-->

<div class="container is-fluid mb-6">
	<h1 class="title">Recursos</h1>
	<h2 class="subtitle">Actualizar recurso</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
		include "./inc/btn_back.php";

		require_once "./php/main.php";

		$id = (isset($_GET['resource_id_up'])) ? $_GET['resource_id_up'] : 0;
		$id=limpiar_cadena($id);

		// Verificando recurso
		$check_recurso=conexion();
		$check_recurso=$check_recurso->query("SELECT * FROM t_recurso WHERE recurso_id='$id'");

		if($check_recurso->rowCount()>0){
			$datos=$check_recurso->fetch();
	?>

	<div class="form-rest mb-6 mt-6"></div>

	<h2 class="title has-text-centered"><?php echo $datos['recurso_nombre']; ?></h2>

	<form action="./php/recurso_actualizar.php" method="POST" class="FormularioAjax" autocomplete="off" >

		<input type="hidden" name="recurso_id" value="<?php echo $datos['recurso_id']; ?>" required >

		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Código</label>
					<input class="input" type="text" name="recurso_codigo" pattern="[A-Z]{3}-[0-9]{4}" maxlength="8" required value="<?php echo $datos['recurso_codigo']; ?>" >
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Nombre</label>
					<input class="input" type="text" name="recurso_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,50}" maxlength="50" required value="<?php echo $datos['recurso_nombre']; ?>" >
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Precio</label>
					<input class="input" type="text" name="recurso_precio" pattern="[0-9.]{1,25}" maxlength="25" required value="<?php echo $datos['recurso_precio']; ?>" >
				</div>
			</div>
			<div class="column">
				<label>Aula</label><br>
				<div class="select is-rounded">
					<select name="recurso_aula" >
						<?php
							$aulas=conexion();
							$aulas=$aulas->query("SELECT * FROM t_aula");
							if($aulas->rowCount()>0){
								$aulas=$aulas->fetchAll();
								foreach($aulas as $row){
									if($datos['aula_id']==$row['aula_id']){
										echo '<option value="'.$row['aula_id'].'" selected="" >'.$row['aula_nombre'].' (Actual)</option>';
									}else{
										echo '<option value="'.$row['aula_id'].'" >'.$row['aula_nombre'].'</option>';
									}
								}
							}
							$aulas=null;
						?>
					</select>
				</div>
			</div>

			<div class="column">
				<label>Estado</label><br>
				<div class="select is-rounded">
					<select name="recurso_estado" >
						<?php
							$estados=conexion();
							$estados=$estados->query("SELECT * FROM t_estado");
							if($estados->rowCount()>0){
								$estados=$estados->fetchAll();
								foreach($estados as $row){
									if($datos['estado_id']==$row['estado_id']){
										echo '<option value="'.$row['estado_id'].'" selected="" >'.$row['estado_descripcion'].' (Actual)</option>';
									}else{
										echo '<option value="'.$row['estado_id'].'" >'.$row['estado_descripcion'].'</option>';
									}
								}
							}
							$estados=null;
						?>
					</select>
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