<!--
	* Nombre del fichero: resource_new.php
	* Descripción: Formulario para crear un nuevo recurso
	* 
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	* 
	* Parámetros de entrada: Ninguno
	* 
	* Salida: Ninguno
-->

<div class="container is-fluid mb-6">
	<h1 class="title">Recursos</h1>
	<h2 class="subtitle">Nuevo recurso</h2>
</div>

<div class="container pb-6 pt-6">
	<?php
		require_once "./php/main.php";
	?>

	<div class="form-rest mb-6 mt-6"></div>

	<form action="./php/recurso_guardar.php" method="POST" class="FormularioAjax" autocomplete="off" enctype="multipart/form-data" >
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Código</label>
					<input class="input" type="text" name="recurso_codigo" pattern="[A-Z]{3}-[0-9]{4}" maxlength="8" placeholder="[A-Z]{3}-[0-9]{4}" title="3 letras mayúsculas y 4 dígitos separados por -" required >
				</div>
			</div>
			<div class="column">
				<div class="control">
					<label>Nombre</label>
					<input class="input" type="text" name="recurso_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,50}" maxlength="50" placeholder="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,50}" title="Puede contener letras, números, acentos, ñ, paréntesis, puntos, comas, signos de dólar, guiones y espacios. Máximo 50 caracteres." required >
				</div>
			</div>
		</div>
		<div class="columns">
			<div class="column">
				<div class="control">
					<label>Precio</label>
					<input class="input" type="text" name="recurso_precio" pattern="[0-9]+(\.[0-9]{1,2})?" maxlength="25" placeholder="[0-9]{2,22}(,[0-9]{1,2})?" title="Número de dos decimales de hasta 23 cifras la parte entera" required >
				</div>
			</div>
			<div class="column">
				<label>Aula</label><br>
				<div class="select is-rounded">
					<select name="recurso_aula" >
						<option value="" selected="" >Seleccione una opción</option>
						<?php
							$aulas=conexion();
							$aulas=$aulas->query("SELECT * FROM t_aula");
							if($aulas->rowCount()>0){
								$aulas=$aulas->fetchAll();
								foreach($aulas as $row){
									echo '<option value="'.$row['aula_id'].'" >'.$row['aula_nombre'].'</option>';
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
		
		<div class="columns">
			<div class="column">
				<label>Foto o imagen del recurso</label><br>
				<div class="file is-small has-name">
					<label class="file-label">
						<input class="file-input" type="file" name="recurso_foto" accept=".jpg, .png, .jpeg" >
						<span class="file-cta">
							<span class="file-label">Imagen</span>
						</span>
						<span class="file-name">JPG, JPEG, PNG. (MAX 3MB)</span>
					</label>
				</div>
			</div>
		</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-info is-rounded">Guardar</button>
		</p>
	</form>
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