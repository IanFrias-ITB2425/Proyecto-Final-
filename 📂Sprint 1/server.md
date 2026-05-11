# Documentación del Proyecto Final - Infraestructura Cloud

Este documento detalla la configuración del servidor y la base de datos para el Proyecto Final.

### 1. Resumen de la Instancia AWS (EC2)
Se ha desplegado una instancia **t3.micro** con Ubuntu 24.04 LTS. En la captura se observa la IP pública asignada y el estado "En ejecución", lo cual confirma que el servidor está operativo.
![Resumen AWS](../imagenes%20rehan/0.png)

### 2. Configuración de Seguridad (Firewall)
Se han configurado las **Security Groups** en el panel de AWS para permitir el tráfico de entrada esencial:
* **Puerto 80 (HTTP):** Acceso web.
* **Puerto 22 (SSH):** Gestión remota.
* **Puerto 443 (HTTPS):** Acceso web seguro.
![Reglas de Entrada](../imagenes%20rehan/1.png)

### 3. Conexión Remota vía SSH
Acceso al servidor mediante el terminal local utilizando una llave privada `.pem`. Se han asignado los permisos correctos (`chmod +x`) antes de iniciar la sesión como usuario `ubuntu`.
![Conexión SSH](../imagenes%20rehan/2.png)

### 4. Instalación del Servidor Web Nginx
Instalación del motor de servidor web **Nginx**. Este será el encargado de servir la aplicación PHP a los usuarios.
![Nginx](../imagenes%20rehan/3.png)

### 5. Instalación de MariaDB
Despliegue del motor de base de datos **MariaDB Server**. Se ha verificado que el sistema descarga y prepara los paquetes correctamente desde los repositorios oficiales.
![MariaDB](../imagenes%20rehan/4.png)

### 6. Configuración de PHP y Extensiones
Instalación del motor **PHP** junto con el módulo `php-mysql` necesario para que el código PHP pueda interactuar con la base de datos MariaDB.
![PHP](../imagenes%20rehan/5.png)

### 7. Securización de la Base de Datos
Ejecución del comando `mysql_secure_installation`. En este paso se define la contraseña del usuario root y se eliminan accesos inseguros por defecto.
![Seguridad DB](../imagenes%20rehan/6.png)

### 8. Gestión de Usuarios y Permisos SQL
Creación de la base de datos `arena_db` y el usuario específico `arena_sys`. Se han otorgado todos los privilegios sobre la base de datos necesaria para el proyecto.
![Usuarios SQL](../imagenes%20rehan/7.png)

### 9. Estructura de Tablas (Logs)
Creación de la tabla `logs_ataques` dentro de `arena_db`. Esta tabla incluye campos como `id`, `origen_ip`, `tipo_incidente` y un `timestamp` automático.
![Tablas](../imagenes%20rehan/8.png)

### 10. Despliegue del Código Fuente
Uso de `git clone` para descargar el repositorio directamente en la ruta `/var/www/html/`. Se han ajustado los permisos de propietario (`chown`) al usuario `www-data` para que el servidor web pueda leer los archivos.
![Git Clone](../imagenes%20rehan/9.png)


### 11. Instalación de Certbot para HTTPS
Preparación del servidor para el cifrado SSL/TLS. Se instalan los paquetes de `certbot` y su plugin para Nginx, asegurando que las comunicaciones con el servidor sean seguras.
![Instalación Certbot](../imagenes%20rehan/10.png)

### 12. Configuración de Dominio Dinámico (DuckDNS)
Se ha vinculado la IP pública de la instancia de AWS (`34.234.144.104`) con el dominio `cyberarena-rehan.duckdns.org`. Esto permite acceder al servidor mediante un nombre fácil de recordar en lugar de la dirección IP.
![Configuración DNS](../imagenes%20rehan/11.png)

### 13. Obtención y Despliegue del Certificado SSL
Ejecución de Certbot para obtener un certificado gratuito de Let's Encrypt. El proceso configura automáticamente Nginx para redirigir el tráfico HTTP a HTTPS de forma segura.
![Certificado SSL](../imagenes%20rehan/12.png)

### 14. Configuración del Virtual Host en Nginx
Ajuste del parámetro `server_name` en el archivo de configuración de Nginx para que coincida exactamente con el dominio registrado en DuckDNS.
![Server Name](../imagenes%20rehan/13.png)

### 15. Dashboard de Control "CyberArena"
Vista del panel de control operativo. Se confirma que los servicios de **Web Server (Nginx)**, **Database (MariaDB)** y **SIEM Telemetry (Wazuh)** están en estado "Nominal" u "Online".
![Dashboard](../imagenes%20rehan/14.png)

### 16. Estructura Final de la Base de Datos
Creación de las tablas principales para la aplicación: `usuarios` (con roles de admin/viewer) y `alertas_reales` (con soporte para logs en formato JSON). La base de datos `arena_db` queda completamente operativa con 3 tablas.
![Tablas Finales](../imagenes%20rehan/15.png)

### 17. Verificación del Sistema Operativo
Resumen del sistema tras la configuración: Ubuntu 24.04.4 LTS funcionando en AWS con una carga de sistema mínima, confirmando la eficiencia del despliegue.
![Info Sistema](../imagenes%20rehan/16.png)
