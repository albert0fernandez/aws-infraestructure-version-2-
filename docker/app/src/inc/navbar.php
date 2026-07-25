<!--
 * Nombre del fichero: navbar.php
 * Descripción: Este fichero contiene el código HTML y PHP para la barra de navegación del sitio web.
 * Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
 * Fecha: Marzo 2025
 * Parámetros de entrada: Ninguno
 * Parámetros de salida: Ninguno
-->

<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="index.php?vista=home">
        <img src="./img/logo.png" width="65" height="28">
        </a>
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start">
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link" >Quienes somos</a>
                <div class="navbar-dropdown">
                    <a class="navbar-item" href="index.php?vista=home">Nuestra historia</a>
                </div>
            </div>

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">Aulas</a>

                <div class="navbar-dropdown">
                    <a href="index.php?vista=room_new" class="navbar-item">Nueva</a>
                    <a href="index.php?vista=room_list" class="navbar-item">Lista</a>
                    <a href="index.php?vista=room_search" class="navbar-item">Buscar</a>
                </div>
            </div>

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">Historial de uso</a>

                <div class="navbar-dropdown">
                    <a href="index.php?vista=history_use_new" class="navbar-item">Nuevo</a>
                    <a href="index.php?vista=history_use_list" class="navbar-item">Lista</a>
                    <a href="index.php?vista=history_use_search" class="navbar-item">Buscar</a>
                </div>
            </div>

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">Recursos</a>

                <div class="navbar-dropdown">
                    <a href="index.php?vista=resource_new" class="navbar-item">Nuevo</a>
                    <a href="index.php?vista=resource_list" class="navbar-item">Lista</a>
                    <a href="index.php?vista=resource_room" class="navbar-item">Por aulas</a>
                    <a href="index.php?vista=resource_search" class="navbar-item">Buscar</a>
                    <a href="index.php?vista=resource_upload_file" class="navbar-item">Carga masiva</a>
                </div>
            </div>
            
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">Usuarios</a>

                <div class="navbar-dropdown">
                    <a href="index.php?vista=user_new" class="navbar-item">Nuevo</a>
                    <a href="index.php?vista=user_list" class="navbar-item">Lista</a>
                    <a href="index.php?vista=user_search" class="navbar-item">Buscar</a>                 
                </div>
            </div>

        <!--Nuevo campo agregado DEPARTAMENTO-->
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">Departamento</a>
                
                <div class="navbar-dropdown">
                    <a href="index.php?vista=departamento_new" class="navbar-item">Nuevo</a>
                    <a href="index.php?vista=departamento_list" class="navbar-item">Lista</a>
                    <a href="index.php?vista=departamento_buscar" class="navbar-item">Buscar</a>
                    <a href="index.php?vista=departamento_masiva" class="navbar-item">Carga Masiva</a>
                </div>
            </div>

        <!--Nuevo campo agregado Proveedor-->
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">Proveedor</a>

                <div class="navbar-dropdown">
                    <a href="index.php?vista=proveedor_new" class="navbar-item">Nuevo</a>
                    <a href="index.php?vista=proveedor_list" class="navbar-item">Lista</a> <!--Falta crear formulario-->
                    <a href="index.php?vista=proveedor_buscar" class="navbar-item">Buscar</a> <!--Falta crear formulario-->
                    <a href="index.php?vista=proveedor_masiva" class="navbar-item">Carga Masiva</a> 
            </div>

        <!--Nuevo campo agregado H.mantenimiento-->
        <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">H.Mantenimiento</a>

                <div class="navbar-dropdown">
                    <a href="index.php?vista=mantenimiento_new" class="navbar-item">Nuevo</a>  
                    <a href="index.php?vista=mantenimiento_list" class="navbar-item">Lista</a>  <!--Falta crear formulario-->
                    <a href="index.php?vista=mantenimiento_buscar" class="navbar-item">Buscar</a>  <!--Falta crear formulario-->
                </div>
            </div>

        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <a href="index.php?vista=user_update&user_id_up=<?php echo $_SESSION['id']; ?>" class="button is-primary is-rounded">
                        Mi cuenta
                    </a>

                    <a href="index.php?vista=logout" class="button is-link is-rounded">
                        Salir
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>