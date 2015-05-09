# Demo LECTOR RSS con MFP5 MVC

El ejemplo hace uso de [MFP5 MVC](https://github.com/jh2odo/mfp5-mvc "GitHub MFP5 MVC") (core) como base de su funcionamiento.

Forma parte del repositorio [MFP5 MVC Examples](https://github.com/jh2odo/mfp5-mvc-examples "GitHub MFP5 MVC Examples")

Incluye una libreria externa [SimplePie](https://github.com/simplepie/simplepie/ "SimplePie") para 
leer los rss. Ha sido modificada y extendida en este ejemplo.

## CONFIGURACIÓN
  
1. Configurar los parametros de su aplicación los ficheros: 

    - /demo/app/configuracion/configuracion.php
    - /demo/app/configuracion/rutas.php
    - /demo/app/configuracion/cache.php

2. Crear la base de datos "lector" e importar el fichero /lector/docs/demo.sql para que funcione correctamente, en 
el caso de instalación sin vagrant.

3. Ir al navegador que utilices y comprobar que está funcionando http://mfp5.dev/lector

Es hora de crear y modificar para desarrollar.

### ACCESO MYSQL (Bases de datos)

Existen dos modos de acceso, mediante la aplicación web integrada en la máquina virtual, PHPMyAdmin o por conexión 
remota desde la maquina local.

- Host: mfp5.dev
- Puerto: 3306
- Usuario: root
- Contraseña: 1234
- Base de Datos: lector

PHPMyAdmin: http:///mfp5.dev/phpmyadmin