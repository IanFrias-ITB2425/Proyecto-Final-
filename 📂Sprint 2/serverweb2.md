# Documentación Proyecto Final - Sprint 2: Seguridad y Monitorización SIEM

En este segundo Sprint, el enfoque principal ha sido el endurecimiento (hardening) del servidor, la implementación de un Firewall de Aplicación Web (WAF) y el despliegue de un sistema de monitorización de eventos de seguridad (Wazuh).

### 18. Salto de Instancia y Gestión de Red Privada
Configuración de acceso seguro mediante salto de red. Se utiliza el terminal para conectar con instancias dentro de la subred privada de AWS (IP 10.0.1.185), asegurando que los servicios críticos no estén expuestos directamente a internet sin un túnel previo.
![Paso 17](../imagenes%20rehan/17.png)

### 19. Instalación de ModSecurity para Nginx
Despliegue del módulo **ModSecurity**, un firewall de aplicación web (WAF) de código abierto. Este componente permite al servidor inspeccionar el tráfico HTTP entrante y bloquear ataques antes de que lleguen a la aplicación.
![Paso 18](../imagenes%20rehan/18.png)

### 20. Implementación de Reglas OWASP CRS
Instalación del **Core Rule Set (CRS)** de OWASP. Estas reglas proporcionan protección genérica contra las vulnerabilidades más comunes encontradas en aplicaciones web, como inyección SQL y Cross-Site Scripting (XSS).
![Paso 19](../imagenes%20rehan/19.png)

### 21. Configuración del Modo de Bloqueo Activo
Ajuste del archivo de configuración `modsecurity.conf`. Se cambia la directiva `SecRuleEngine` de `DetectionOnly` a `On` para que el servidor bloquee activamente las peticiones sospechosas con un error 403 Forbidden.
![Paso 20](../imagenes%20rehan/20.png)

### 22. Despliegue del Agente de Seguridad Wazuh
Instalación del agente **Wazuh** en el servidor Ubuntu. Este agente se encarga de recolectar logs, monitorizar la integridad de archivos y detectar rootkits para enviarlos al servidor central de seguridad.
![Paso 21](../imagenes%20rehan/21.png)

### 23. Verificación del Servicio Wazuh-Agent
Comprobación mediante `systemctl` del estado del agente. Se confirma que el servicio está activo y correctamente comunicado con el Manager, garantizando que el servidor está bajo supervisión constante.
![Paso 22](../imagenes%20rehan/22.png)

### 24. Integración de Logs con MariaDB
Configuración de los conectores de base de datos para registrar las alertas de seguridad directamente en las tablas de `arena_db`. Esto permite tener un histórico persistente de los intentos de ataque bloqueados por el WAF.
![Paso 23](../imagenes%20rehan/23.png)

### 25. Monitorización de Carga y Recursos
Uso de herramientas de monitorización de procesos para asegurar que la capa de seguridad (WAF + Wazuh) no sature los recursos de la instancia **t3.micro**, manteniendo un equilibrio entre protección y rendimiento.
![Paso 24](../imagenes%20rehan/24.png)

### 26. Reinicio y Test de Integridad de Servicios
Ejecución de un reinicio controlado de Nginx para validar que la sintaxis de las nuevas reglas de seguridad es correcta y que todos los servicios inician automáticamente tras una actualización.
![Paso 25](../imagenes%20rehan/25.png)

### 27. Acceso Final mediante Dominio Seguro
Prueba de conectividad final a través del dominio `cyberarena-rehan.duckdns.org` bajo protocolo HTTPS. El servidor está ahora protegido por WAF y monitorizado por Wazuh de extremo a extremo.
![Paso 26](../imagenes%20rehan/26.png)
