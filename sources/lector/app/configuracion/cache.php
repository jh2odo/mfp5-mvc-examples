<?php
// Por defecto se cachean todas las paginas. (igual a no crear este archivo)
// Solo una regla de cache es ejecutada cuando es coincidente.
// En este fichero se definen las reglas para las url que NO se van a cachear.
// preg_match
// Ejemplo:
//			$rutas[] = array("/^\bcontacto\b/i",array(0,"contacto","inicio/contacto"));
// 		La rauta que habria que poner para no cachear esta pagina seria:
//			$nocaches[] = "/^\bcontacto\b/i";

$nocaches[] = "/^\bsite\b/i"; //xml
$nocaches[] = "/^\bcontacto\b/i"; //formulario
$nocaches[] = "/^\bsugerirmedio\b/i"; //formulario
$nocaches[] = "/^\bestado\b/i"; //estado noticias

?>