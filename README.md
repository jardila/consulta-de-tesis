Sistema de Consulta de Tesis de Grado
=======================

Introduction
------------
Ante todo me es necesario dar todo el credito de este proyecto a Dios, sin dejar de lado la institucion en la cual me he formado como Ingeniero "<a href="http://www.unefa.edu.ve/zulia/zulia.php">UNEFA</a>", asi como tambien a mis compañeros de clase.
Este proyecto esta trabajado bajo la filosofia MVC. El framework utilizado es el Zend en su version 2.2.0dev.



Installation
------------
Para comenzar, el proceso de instalacion o preparacion para desarrollo esta basado en la documentacion de <a href="http://framework.zend.com/manual/2.0/en/ref/installation.html">Zend Framewor 2</a> sin embargo solo es necesario configurar el host virtual para dar inicio con el proyecto.

Pasos para la configuracion en Linux (Debian 7)

Primero procedemos a descargarnos la aplicacion desde este repositorio en el directorio donde lo trabajaremos, ejemplo: /var/www/proyecto/.
Debemos descargar el proyecto y lo descomprimimos alli
Luego procedemos a la configuracion basica del virtualhost

    # sudo nano /etc/apache2/sites-available/sitio.dominio

Alli colocamos el siguiente codigo modificando lo que es de cada quien:

    <VirtualHost *:80>
        ServerName sitio.dominio
        ServerAlias sitio.dominio
        DocumentRoot "/var/www/proyecto/public"
        SetEnv APPLICATION_ENV "development"
        <Directory "/var/www/proyecto/public">
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>

Esto es para los que quieran esta configuracion. Si tienes un ambiente de trabajo puedes hacerlo a tu manera.
Luego procedemos a configurar el nombre del host en el SO

    sudo nano /etc/hosts

Alli agregamos en la lista 

    127.0.0.1   sitio.dominio   localhost
    
Una vez terminado este paso procedemos a habilitar el sitio con:

    sudo a2ensite sitio.dominio
    
Para los que no han trabajado con Zend Framework y no sabes este requerimiento es necesario tener habilitado el modulo de apache rewrite, podemos hacerlo mediante este comando:

    sudo a2enmod rewrite

al terminar esto procedemos a reiniciar apache:

    sudo service apache2 restart

Listo! ya podemos utilizar o modificar la aplicacion
