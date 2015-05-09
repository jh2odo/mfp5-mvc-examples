<?php

$app = substr(dirname(__FILE__), strlen(dirname(dirname(__FILE__))) + 1); // Para detectar la carpeta de la app

require_once('../core/inicializar.php');

ControladorFrontal::ejecutar();

exit;

?>