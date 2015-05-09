# Ejemplos y demos de uso MFP5 MVC

Los ejemplos hacen uso de [MFP5 MVC](https://github.com/jh2odo/mfp5-mvc "GitHub MFP5 MVC") (core) como base de su funcionamiento.

En la inicialización y provisionamiento de la máquina virtual, se descargará e instalará automáticamente la última 
versión del paquete MFP5 MVC desde su repositorio. 

Todo el material es funcional y puede ser utilizando para cualquier uso, pero puede contener errores.

## MFP5 MVC (core)

El mini framework es un ejemplo de implementación y carga de clases sin el uso del nombrado de clases, y el uso 
recomendado es didáctico en la instrodución del patrón modelo vista controlador (MVC) en español.

Preparado para ser utilazado en versiones de PHP 5.2 y superiores. Originalmente está desarrollado para PHP 5.2 porque 
no hace uso de las principales novedades con la llegada de PHP 5.3, como los "namespaces" y herramientas como 
[Composer](https://getcomposer.org/ "Composer"), lo cual a día de hoy es recomendable y obligatorio usarlos.

En la actualidad, existen framework profesionales que hacen mucho mas simple los proyectos PHP, como son: 
[Laravel](https://getcomposer.org/ "Laravel"), [Symfony](https://getcomposer.org/ "Symfony"), 
[Silex](https://getcomposer.org/ "Silex"), [Lumen](https://getcomposer.org/ "Lumen"), etc.

Código fuente: [https://github.com/jh2odo/mfp5-mvc](https://github.com/jh2odo/mfp5-mvc "GitHub MFP5 MVC")

## DEMOS

- [Lector y comparador de noticias (RSS)](https://github.com/jh2odo/mfp5-mvc-examples/sources/lector "Lector RSS con MFP5") - Ejemplo real: www.prensaseria.net
- *Blog [PRÓXIMAMENTE]*

## EJEMPLOS *[PRÓXIMAMENTE]*

- *Uso de Caché de elementos y de página*
- *Envio de Correos electrónicos*
- *Web Multilenguaje (varios idiomas)*
- *Detección de dispositivos y sistemas operativos (móvil, pc, linux, windows...)*
- *Uso de PDFs*

## INSTALACIÓN

### SOFTWARE

Linux (all apt-get)
- vagrant (1.7.2)
- vagrant-vbguest
- virtualbox (4.3)
- git

Windows
- vagrant (https://www.vagrantup.com/downloads.html)
- virtualbox (https://www.virtualbox.org/wiki/Downloads)
- git (http://git-scm.com/downloads) o GitHub UI + Shell

### DESCARGAR CÓDIGO FUENTE O CLONAR CON GIT

Descargar o clonar el repositorio: https://github.com/jh2odo/mfp5-mvc-examples

Ejemplo:

    - Linux: git clone https://github.com/jh2odo/mfp5-mvc-examples /home/user/proyectos/mfp5-mvc-examples
    - Windows: Descargar y descomprimir ZIP in D:\proyectos\mfp5-mvc-examples

Tras la descarga los directorios serán:

    - /provision/ -> archivos de configuración para la inicialización y configuración de la máquina virtual
    - /sources/ -> código fuente
    - /Vagrantfile -> fichero de configuración de vagrant

### CONFIGURACIÓN

IP Páblica: selecciona una IP libre dentro de tu red, ejemplo, 192.168.1.101
IP Privada: ejemplo, 10.0.0.10

Es necesario cambiar la IP en 2 ficheros locales (hosts y Vangranfile) para el nombrado de dominios en nuestra propia máquina:

1. En el fichero "hosts" del PC, hay que añadir en modo administrador: 

    192.168.1.101   mfp5.dev
    10.0.0.10       mfp5.dev

Ejemplo:

    - Linux: /etc/hosts
    - Windows: C:\Windows\System32\drivers\etc\hosts

2. En el fichero Vagrantfile:

    web.vm.network :public_network, ip: "192.168.1.101"
    web.vm.network :private_network, ip: "10.0.0.10"

## INICIALIZACIÓN (primer uso)

1. Iniciar GitBash o GitHub Shell (Cgywin en Windows) o el Terminal (Linux) 
2. Ejecutar el siguiente comando:  

    vagrant box add lucid32 http://files.vagrantup.com/lucid32.box

3. Ir al directorio donde está la descarga o el clonado del respositorio mediante el comando "cd"

    - Linux: cd /home/user/proyectos/mfp5-mvc-examples
    - Windows: cd D:\proyectos\mfp5-mvc-examples

4. Ejecutar: vagrant up mfp5
5. Ir al navegador que utilices: http://mfp5.dev
6. Crear y modificar los ejemplos y demos para probarlos

## USO

### ADMINISTRACIÓN DE MÁQUINAS VIRTUALES CON VAGRANT

- Inicio: vagrant up mfp5
- Parar: vagrant halt mfp5
- Acceso SSH: vagrant ssh mfp5
- Estado: vagrant status mfp5
- Eliminar: vagrant destroy mfp5

### ACCESO WEBS

http://mfp5.dev

### ACCESO MYSQL (Bases de datos)

Existen dos modos de acceso, mediante la aplicación web integrada en la máquina virtual, PHPMyAdmin o por conexión 
remota desde la maquina local.

- Host: mfp5.dev
- Puerto: 3306
- Usuario: root
- Contraseña: 1234

PHPMyAdmin: http:///mfp5.dev/phpmyadmin







