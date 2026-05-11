
# Documentación Proyecto Final - Sprint 2: Seguridad y SIEM

### 17. Acceso a la terminal y preparación de claves
Inicio de la sesión en el entorno local. Se observa la lista de archivos donde se encuentra la clave `.pem` necesaria para la administración de las instancias de AWS.
![Paso 17](../imagenes%20rehan/17.png)

### 18. Instalación de dependencias de ModSecurity
Ejecución de `sudo apt install` para instalar el conector de ModSecurity y las librerías necesarias para el filtrado de tráfico en el servidor web.
![Paso 18](../imagenes%20rehan/18.png)

### 19. Configuración del Core Rule Set (CRS) de OWASP
Configuración de las reglas de seguridad. Se muestra la edición del archivo para incluir las reglas que protegen contra ataques de inyección y vulnerabilidades web conocidas.
![Paso 19](../imagenes%20rehan/19.png)

### 20. Activación del Motor de Reglas (modsecurity.conf)
Edición del archivo de configuración principal de ModSecurity. Se cambia la directiva `SecRuleEngine DetectionOnly` a `SecRuleEngine On` para permitir el bloqueo de ataques en tiempo real.
![Paso 20](../imagenes%20rehan/20.png)

### 21. Despliegue del Agente Wazuh vía repositorio
Descarga e instalación del agente de **Wazuh** utilizando el gestor de paquetes. Se prepara el servidor para integrarse con el SIEM centralizado.
![Paso 21](../imagenes%20rehan/21.png)

### 22. Verificación del estado del Agente Wazuh
Ejecución de `systemctl status wazuh-agent`. Se confirma que el servicio está **active (running)** y correctamente iniciado en el sistema.
![Paso 22](../imagenes%20rehan/22.png)

### 23. Configuración de la Base de Datos para Logs
Acceso a MariaDB para configurar las tablas donde se registrarán los eventos de seguridad. Se preparan los permisos para que los servicios de monitorización puedan escribir datos.
![Paso 23](../imagenes%20rehan/23.png)

### 24. Monitorización de recursos con TOP
Uso del comando `top` para verificar el impacto de los nuevos servicios de seguridad en la CPU y la memoria RAM, asegurando la estabilidad de la instancia.
![Paso 24](../imagenes%20rehan/24.png)

### 25. Comprobación de configuración y reinicio de Nginx
Validación de la sintaxis de los archivos de configuración y reinicio del servidor web para aplicar los cambios del firewall (WAF).
![Paso 25](../imagenes%20rehan/25.png)

### 26. Acceso final al sitio web bajo HTTPS
Prueba final de carga del dominio `cyberarena-rehan.duckdns.org` en el navegador. Se confirma que el sitio es accesible y que el certificado SSL está funcionando correctamente.
![Paso 26](../imagenes%20rehan/26.png)
