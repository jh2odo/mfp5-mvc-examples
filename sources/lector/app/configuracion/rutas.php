<?php
// Orden del routing importante a tener encuenta
// Solo regla por enrrutamiento - en cuantro se cumpla una, no se procesan mas reglas.
// El primer parametro es un patron para el preg_match
// directo
//$rutas[] = array("/aaa/i","aaa/");

// Acortamiento de direcciones
$rutas[] = array("/^\bcontacto\b/i", array(0, "contacto", "inicio/contacto"));
$rutas[] = array("/^\bsugerirmedio\b/i", array(0, "sugerirmedio", "inicio/sugerirmedio"));
$rutas[] = array("/^\bmapadelsitio\b/i", array(0, "mapadelsitio", "inicio/mapadelsitio"));
$rutas[] = array("/^\bsite\b/i", array(0, "site", "inicio/site"));
$rutas[] = array("/^\baccesibilidad\b/i", array(0, "accesibilidad", "inicio/accesibilidad"));

$rutas[] = array("/^\bestado\b/i", array(0, "estado", "noticias/estado"));

$rutas[] = array("/^sitemap\.xml/i", array(0, "sitemap.xml", "inicio/site"));
$rutas[] = array("/^favicon\.ico/i", array(0, "favicon.ico", "redireccion/favicon/")); // Para corregir peticiones a /favicon.ico

$rutas[] = array("/^\bnoticias\/lector\b/i", array(0, "noticias/lector", "noticias/lector")); // Excepcion para no afectar a noticias/Ver
$rutas[] = array("/^\bnoticias\/\b/i", array(0, "noticias/", "noticias/ver/"));


// preg_replace
//$rutas[] = array("noticias/",array(1,'/noticias\//','noticias/ver/'));
?>