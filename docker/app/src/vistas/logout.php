<?php
    /**
	* Nombre del fichero: logout.php
	* Descripción: Este script destruye la sesión y redirige al usuario a la página de login.
	*
	* Autor: RetaCantabria - ASIR1 - Dpto. Informática - IES Alisal
	* Fecha: Marzo 2025
	*
	* Parámetros de entrada: Ninguno
	* Salida: Ninguno
	*/

	// Destruir la sesión
	session_destroy();

	// Comprobar si se han enviado encabezados
	if(headers_sent()){
		// Redirigir usando JavaScript si los encabezados ya se han enviado
		echo "<script> window.location.href='index.php?vista=login'; </script>";
	}else{
		// Redirigir usando encabezados HTTP si los encabezados no se han enviado
		header("Location: index.php?vista=login");
	}