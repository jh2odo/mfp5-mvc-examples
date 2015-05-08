# Ejemplos y demos de uso del Mini Framework PHP 5 : MFP5 MVC

## DEMOS

- Lector y comparador de noticias (RSS) - Aplicación Real www.prensaseria.net
- Blog [PRÓXIMAMENTE]

## EJEMPLOS [PRÓXIMAMENTE]

- Uso de Caché de elementos y de página 
- Envio de Correos electrónicos
- Web Multilenguaje (varios idiomas)
- Detección de dispositivos y sistemas operativos (móvil, pc, linux, windows...)
- Uso de PDFs

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

El acceso remoto desde fuera de la maquina virtual está permitido.

- Host: mfp5.dev
- Puerto: 3306
- Usuario: root
- Contraseña: 1234




