<?php

// HOST, BASE, DOMINIO, LENGUAJE, CODIFICACION...
$config->set('HOST', 'mfp5.dev/lector'); // Obligatorio
$config->set('BASE_URL', "http://mfp5.dev/lector/"); // Obligatorio
$config->set('DOMINIO', 'mfp5.dev/lector'); // Obligatorio

//$config->set('IDIOMA', 'es'); //Deberia de ser Obligatorio (Para el html) //internalizacion
//$config->set('CHARSET_ENCODING', "utf-8"); //Deberia de ser Obligatorio (iso-8859-1)

// BASE DE DATOS
$config->set('BD_BASEDATOS', 'lector');
$config->set('BD_DRIVER', 'mysql');
$config->set('BD_HOST', 'localhost');
$config->set('BD_USUARIO', 'root');
$config->set('BD_PASSWORD', '1234');
//$config->set('BD_PERSISTENTE', FALSE);

// CACHE(GENERAL)
$config->set('CACHE', TRUE);
$config->set('CACHE_PAGINA', FALSE);
$config->set('CACHE_DURACION', 3 * 60 * 60); // Tres Horas

// DEBUG
$config->set('DEBUG', TRUE); //(Obligatorio)
//$config->set('DEBUG_MODO', 'a'); // Genera html en un fichero (b) o vuelca en pantalla el html (a)

// MOSTRAR ERRORES (DESARROLLO:on Y PRODUCCION:off)
$config->set('DISPLAY_ERRORS', 'on');

$config->set('CONTROLADOR_ERROR', FALSE); // FALSE ó TRUE

// EMAIL (función mail php)
$config->set("EMAIL_DE", "admin@localhost"); // Obligatorio
$config->set("EMAIL_PARA", "admin@localhost");// Obligatorio

// DEPENDIENTE DE LA APLICACION - DEFINIDO POR EL USUARIO

// CACHE RSS - SIMPLEPIE
$config->set('RSS_CACHE', TRUE);
$config->set('RSS_CACHE_PATH', BASE_PATH_APP . 'app' . DIRSEP . 'cache' . DIRSEP . 'rss' . DIRSEP);
$config->set('RSS_CACHE_DURACION', 3 * 60 * 60); // 3 Horas
$config->set('RSS_CACHE_TIMEOUT', 7); // Segundos
$config->set('RSS_CACHE_IMAGENES', TRUE); // Segundos
$config->set('RSS_CACHE_IMAGENES_PATH', BASE_PATH_APP . 'app' . DIRSEP . 'cache' . DIRSEP . 'rss' . DIRSEP . 'imagenes' . DIRSEP);

$config->set('GOOGLE_ANALYTIC', FALSE);
$config->set('GOOGLE_ANALYTIC_CODE', 'UA-XXXXXX-X');


date_default_timezone_set('Madrid/Europe');

?>
