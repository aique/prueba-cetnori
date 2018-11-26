# Prueba Irontec

## Instalación del servidor

### Creación de un host virtual

- Creamos un dominio en el fichero `/etc/hosts` apuntando a nuestro equipo local.
```
127.0.0.1       prueba-irontec-server.com
```
- Creamos un host virtual en apache.
    - Creamos el fichero de configuración en el directorio `/etc/apache2/sites-available`.
    - Editamos el fichero con el siguiente contenido:
    ```
    <VirtualHost *:80>    
            ServerName prueba-irontec-server.com
            DocumentRoot /home/aique/Projects/prueba-irontec/server
            <Directory /home/aique/Projects/prueba-irontec/server>
                    Options FollowSymLinks Includes
                    AllowOverride All
                    Require all granted
            </Directory>
            ErrorLog ${APACHE_LOG_DIR}/prueba-irontec-server-error.log
            CustomLog ${APACHE_LOG_DIR}/prueba-irontec-server-access.log combined
    </VirtualHost>

    ```

### Descarcar las dependencias
Mediante línea de comandos nos ubicamos dentro de la carpeta `server` del proyecto y ejecutamos `composer install`.

### Crear una base de datos
Será necesario crear una base de datos para el proyecto y guardar los datos de acceso para el siguiente paso.

### Generar el fichero de configuración
Accediendo a la url `http://prueba-irontec-server.com` será necesario seguir los pasos del wizard de Drupal para generar el fichero de configuración.

### Dar de alta los módulos necesarios
Dentro de la carpeta `server` introducimos las siguientes ordenes por línea de comandos:
```
doctrine module:install cars
``` 