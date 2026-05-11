# Documentación Proyecto Final - Sprint 2: Seguridad y Endurecimiento

En esta fase se ha implementado una capa de seguridad avanzada (WAF) y un sistema de monitorización de integridad y eventos (SIEM).

### 18. Acceso SSH y Configuración de Red
Conexión inicial al entorno de trabajo. Se valida el acceso a la terminal y la preparación de la ruta de los archivos PEM para gestionar las instancias de la infraestructura.
![Paso 17](../imagenes%20rehan/17.png)

### 19. Instalación de ModSecurity (libapache2-mod-security2)
Proceso de instalación del motor de **ModSecurity**. Aunque el paquete tiene nombre de Apache, se configura como un módulo dinámico para actuar como Firewall de Aplicación Web en nuestro servidor.
![Paso 18](../imagenes%20rehan/18.png)

### 20. Configuración del CRS (Core Rule Set)
Edición y preparación de las reglas del **OWASP ModSecurity Core Rule Set**. En esta captura se observa la configuración de las reglas base que protegen contra inyecciones y ataques de fuerza bruta.
![Paso 19](../imagenes%20rehan/19.png)

### 21. Activación del motor de seguridad (SecRuleEngine)
Modificación del archivo de configuración principal para activar el filtrado. Se cambia la directiva de `DetectionOnly` (solo detectar) a `On` (bloquear ataques) para que el WAF sea efectivo.
![Paso 20](../imagenes%20rehan/20.png)

### 22. Instalación del Agente Wazuh
Despliegue del agente de monitorización **Wazuh** mediante el gestor de paquetes `apt`. Este agente permitirá la detección de intrusiones a nivel de host (HIDS).
![Paso 21](../imagenes%20rehan/21.png)

### 23. Verificación del Servicio Wazuh-Agent
Uso de `systemctl status wazuh-agent` para confirmar que el servicio está **active (running)**. Esto asegura que el servidor está enviando telemetría de seguridad al manager.
![Paso 22](../imagenes%20rehan/22.png)

### 24. Auditoría de Base de Datos y Logs
Revisión de los logs generados en el servidor. Se verifica la correcta escritura de eventos para que puedan ser cruzados con los datos de MariaDB y mostrar alertas en el dashboard.
![Paso 23](../imagenes%20rehan/23.png)

### 25. Monitorización de Procesos en Tiempo Real
Uso del comando `top` o similar para auditar el consumo de recursos. Se comprueba que los procesos de Wazuh y el WAF no penalizan el rendimiento del servidor web.
![Paso 24](../imagenes%20rehan/24.png)

### 26. Reinicio de Nginx y Validación de Sintaxis
Ejecución de un reinicio del servicio Nginx. Es un paso crítico para asegurar que las nuevas configuraciones de seguridad se han cargado sin errores de sintaxis.
![Paso 25](../imagenes%20rehan/25.png)

### 27. Validación Final del Dashboard Web
Acceso al dominio configurado donde se observa la interfaz del proyecto. Se confirma que el servidor responde correctamente bajo las nuevas reglas de seguridad y cifrado HTTPS.
![Paso 26](../imagenes%20rehan/26.png)
