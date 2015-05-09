<?php
/*
    set_time_limit(0);
    ini_set('display_errors','on'); // On solo en desarrollo y en produccion off
    ini_set('display_startup_errors','on'); // On solo en desarrollo y en produccion off
    error_reporting(E_ALL);
*/

// Inicializamos scripts que no son pasados por el controlador frontal
$app = 'lector';
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'inicializar.php';

$cache_directorio = Cargador::cargar("Configuracion")->RSS_CACHE_IMAGENES_PATH;
if ($cache_directorio === FALSE) {
    $cache_directorio = BASE_PATH_APP . 'app' . DIRSEP . 'cache' . DIRSEP . 'rss' . DIRSEP . 'imagenes' . DIRSEP;
}

if (isset($_GET['cache'])) {

    $imagen = trim(strip_tags($_GET['cache']));

    // Solo Numero y letras
    $caracteres_url_validos = "/[^a-z0-9]/";
    $imagen = preg_replace($caracteres_url_validos, "", $imagen);

    ControladorFrontal::setGestorErroresExcepciones(FALSE);
    Cargador::cargar('Libreria')->SimplePieExtendido;
    SimplePie_Misc::display_cached_file($imagen, $cache_directorio, 'spi');
    ControladorFrontal::setGestorErroresExcepciones(TRUE);
}

exit;
?>
