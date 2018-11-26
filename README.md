# Prueba Irontec

## Instalación del servidor

### Creación de un host virtual

- Creamos un dominio en el fichero `/etc/hosts` apuntando a nuestro equipo local.
```
127.0.0.1       prueba-irontec-server.com
```
- Creamos un host virtual en apache.
    - Creamos el fichero de configuración en el directorio `/etc/apache2/sites-available`
    - Editamos el fichero con el siguiente contenido
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