# Prueba Irontec

## Instalación del servidor

### Creación de un host virtual

- Creamos un dominio en el fichero `/etc/hosts` apuntando a nuestro equipo local.
```
127.0.0.1       prueba-irontec-server.com
```
- Creamos un host virtual en apache.
    - Creamos el fichero de configuración en el directorio `/etc/apache2/sites-available`.
    - Generamos los certificados necesario para utilizar SSL, ya que desde el cliente se enviarán los datos de usuario y esta operación ha de realizarse a través de un canal seguro:
        - Mediante el comando `sudo mkdir /etc/apache2/ssl` se creará el directorio que contendrá los certificados.
        - Los certificados se generarán con la instrucción `sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/apache2/ssl/apache.key -out /etc/apache2/ssl/apache.crt`.
    - Editamos el fichero con el siguiente contenido (realizar las modificaciones pertinentes):
    ```
    <IfModule mod_ssl.c>
        <VirtualHost _default_:443>
            ServerName prueba-irontec-server.com
            DocumentRoot {project-path}/prueba-irontec/server
    
            <Directory /home/aique/Projects/prueba-irontec/server>
                Options FollowSymLinks Includes
                AllowOverride All
                Require all granted
            </Directory>
    
            ErrorLog ${APACHE_LOG_DIR}/prueba-irontec-server-error.log
            CustomLog ${APACHE_LOG_DIR}/prueba-irontec-server-access.log combined
            SSLEngine on
            SSLCertificateFile /etc/apache2/ssl/apache.crt
            SSLCertificateKeyFile /etc/apache2/ssl/apache.key
            <FilesMatch "\.(cgi|shtml|phtml|php)$">
                            SSLOptions +StdEnvVars
            </FilesMatch>
            <Directory /usr/lib/cgi-bin>
                            SSLOptions +StdEnvVars
            </Directory>
            BrowserMatch "MSIE [2-6]" \
                            nokeepalive ssl-unclean-shutdown \
                            downgrade-1.0 force-response-1.0
            BrowserMatch "MSIE [17-9]" ssl-unclean-shutdown
        </VirtualHost>
    </IfModule>

    ```

### Descarcar las dependencias
Mediante línea de comandos nos ubicamos dentro de la carpeta `server` del proyecto y ejecutamos `composer install`.

### Crear una base de datos
Será necesario crear una base de datos para el proyecto y guardar los datos de acceso para el siguiente paso.

### Generar el fichero de configuración
Accediendo a la url `https://prueba-irontec-server.com` será necesario seguir los pasos del wizard de Drupal para generar el fichero de configuración.

### Dar de alta los módulos necesarios
Dentro de la carpeta `server` introducimos las siguientes ordenes por línea de comandos:
```
drupal module:install cars
``` 
La versión recomendada del módulo `RestUI` es `8.x-1.16`.

### Activar el servicio REST
Accediendo a la url `https://prueba-irontec-server.com` y desde la opción de menú `Administration > Configuration > Web Services > REST`, activamos la opción `Cars resource` que se encuentra en el listado con las siguientes opciones:

- Methods: GET
- Accepted request formats: json
- Authentication providers: basic_auth

Una vez hecho esto, vamos a permitir el acceso de usuarios logueados al servicio habilitando la opción `Authenticated  user` del registro `Access GET on Cars resource resource` que aparece en la tabla dentro de la url `https://prueba-irontec-server.com/admin/people/permissions`.

### Insertar datos de prueba
La inserción de datos se realizará mediante el formulario que se encuentra en `https://prueba-irontec-server.com/admin/structure/car/add`.

## Instalación del cliente

### Creación de un host virtual

- Creamos un dominio en el fichero `/etc/hosts` apuntando a nuestro equipo local.
```
127.0.0.1       prueba-irontec-client.com
```
- Creamos un host virtual en apache.
    - Creamos el fichero de configuración en el directorio `/etc/apache2/sites-available`.
    - Editamos el fichero con el siguiente contenido (realizar las modificaciones pertinentes):
    ```
    <IfModule mod_ssl.c>
        <VirtualHost _default_:443>
            ServerName prueba-irontec-client.com
            DocumentRoot {project-path}/prueba-irontec/client
    
            <Directory /home/aique/Projects/prueba-irontec/client>
                Options FollowSymLinks Includes
                AllowOverride All
                Require all granted
            </Directory>
    
            ErrorLog ${APACHE_LOG_DIR}/prueba-irontec-client-error.log
            CustomLog ${APACHE_LOG_DIR}/prueba-irontec-client-access.log combined
            SSLEngine on
            SSLCertificateFile /etc/apache2/ssl/apache.crt
            SSLCertificateKeyFile /etc/apache2/ssl/apache.key
            <FilesMatch "\.(cgi|shtml|phtml|php)$">
                            SSLOptions +StdEnvVars
            </FilesMatch>
            <Directory /usr/lib/cgi-bin>
                            SSLOptions +StdEnvVars
            </Directory>
            BrowserMatch "MSIE [2-6]" \
                            nokeepalive ssl-unclean-shutdown \
                            downgrade-1.0 force-response-1.0
            BrowserMatch "MSIE [17-9]" ssl-unclean-shutdown
        </VirtualHost>
    </IfModule>


    ```
    
### Creación del fichero de configuración

Será necesario copiar el fichero `example.config.json`, modificar el nombre y contraseña del usuario creado durante la instalación de Drupal y guardarlo con el nombre `config.json`. 
    
## Ejecución de la prueba

Llegado este punto ya se puede acceder al cliente mediante la url `https://prueba-irontec-client.com` para consultar los datos que se han introducido en el servidor.

## Screenshots

Vista del detalle de un coche en el servidor (parece que el tema no se lleva bien con `https`...)

![Cliente mostrando el listado de coches](https://raw.githubusercontent.com/aique/prueba-irontec/master/screenshots/irontec_server.png)

Cliente mostrando el listado de coches

![Cliente mostrando el listado de coches](https://raw.githubusercontent.com/aique/prueba-irontec/master/screenshots/irontec_client.png)