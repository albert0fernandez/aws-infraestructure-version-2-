<!--
    * Nombre del fichero: resource_search.php
    * Descripción: Página para buscar y gestionar recursos.
    *
    * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
    * Fecha: Marzo 2025
    *
    * Parámetros de entrada: 
    *   - modulo_buscador (POST)
    *   - busqueda_recurso (SESSION)
    *   - resource_id_del (GET)
    *   - page (GET)
    *   - room_id (GET)
    *
    * Salida: Ninguno
-->



<div class="container is-fluid mb-6">
	<h1 class="title">Recursos</h1>
	<h2 class="subtitle">Buscar recurso</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
        require_once "./php/main.php";

        // Verificar si se ha enviado el formulario de búsqueda
        if(isset($_POST['modulo_buscador'])){
            require_once "./php/buscador.php";
        }

        // Verificar si no hay una búsqueda activa
        if(!isset($_SESSION['busqueda_recurso']) && empty($_SESSION['busqueda_recurso'])){
    ?>
    <div class="columns">
        <div class="column">
            <form action="" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="recurso">
                <div class="field is-grouped">
                    <p class="control is-expanded">
                        <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estas buscando?" title="Introduce el código o nombre de un recurso" pattern="[A-Z]{3}-[0-9]{4}|[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,50}" maxlength="50" >
                    </p>
                    <p class="control">
                        <button class="button is-info is-rounded" type="submit" >Buscar</button>
                    </p>
                </div>
            </form>
        </div>
    </div>
    <?php }else{ ?>
    <div class="columns">
        <div class="column">
            <form class="has-text-centered mt-6 mb-6" action="" method="POST" autocomplete="off" >
                <input type="hidden" name="modulo_buscador" value="recurso"> 
                <input type="hidden" name="eliminar_buscador" value="recurso">
                <p>Estas buscando <strong>"<?php echo $_SESSION['busqueda_recurso']; ?>"</strong></p>
                <br>
                <button type="submit" class="button is-danger is-rounded">Eliminar busqueda</button>
            </form>
        </div>
    </div>
    <?php
            // Eliminar recurso
            if(isset($_GET['resource_id_del'])){
                require_once "./php/recurso_eliminar.php";
            }

            // Verificar la página actual
            if(!isset($_GET['page'])){
                $pagina=1;
            }else{
                $pagina=(int) $_GET['page'];
                if($pagina<=1){
                    $pagina=1;
                }
            }

            $aula_id = (isset($_GET['room_id'])) ? $_GET['room_id'] : 0;

            $pagina=limpiar_cadena($pagina);
            $url="index.php?vista=resource_search&page="; // URL base para el paginador
            $registros=15;
            $busqueda=$_SESSION['busqueda_recurso']; // Término de búsqueda

            // Paginador recurso
            require_once "./php/recurso_lista.php";
        } 
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