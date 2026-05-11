# Documentación Técnica: Implementación de Seguridad y Hardening

Este informe detalla cronológicamente las acciones realizadas para asegurar el servidor, desde la instalación de herramientas de defensa hasta la verificación final de cumplimiento.

---

## 1. Actualización e Instalación de ModSecurity
El primer paso consistió en preparar el sistema operativo e instalar el módulo **ModSecurity** para actuar como Firewall de Aplicación Web (WAF).

![Reglas de Entrada](../imagenes%20rehan/17.png)

## 2. Activación del Motor de Reglas
Se modificó la configuración de ModSecurity para cambiar la directiva `SecRuleEngine` de `DetectionOnly` a `On`, permitiendo así el bloqueo activo de ataques.

![Reglas de Entrada](../imagenes%20rehan/18.png)

## 3. Instalación de Reglas OWASP CRS
Se procedió a instalar el conjunto de reglas de seguridad de **OWASP (Core Rule Set)**, proporcionando una base sólida contra vulnerabilidades web conocidas.

![Reglas de Entrada](../imagenes%20rehan/19.png)

## 4. Validación de Acceso y Pruebas de Bloqueo
Se realizaron pruebas externas con `curl`:
- Un intento de inyección SQL fue bloqueado con un error **403 Forbidden**.
- El acceso normal al sitio web devolvió un código **200 OK**.

![Reglas de Entrada](../imagenes%20rehan/20.png)

## 5. Verificación de la Configuración del Servidor
Se comprobó la integridad de los archivos de configuración de Nginx para asegurar que el módulo de seguridad se cargara correctamente y sin errores de sintaxis.

![Reglas de Entrada](../imagenes%20rehan/21.png)

## 6. Configuración de Inclusión de Reglas y Whitelist
Se configuró el archivo principal para cargar el "cerebro" de OWASP, las reglas de ataque y se habilitó un archivo de lista blanca (whitelist) para excepciones.

![Reglas de Entrada](../imagenes%20rehan/22.png)

## 7. Análisis de Logs de Seguridad
Se examinaron los registros de error del sistema, donde se evidencia el bloqueo detallado de las peticiones maliciosas (ataques SQLi detectados por la regla 942100).

![Reglas de Entrada](../imagenes%20rehan/23.png)

## 8. Integración con SIEM (Agente Wazuh)
Se configuró el agente de **Wazuh** en el servidor, vinculándolo a la IP del servidor de gestión `10.0.1.96` para el monitoreo centralizado de eventos.

![Reglas de Entrada](../imagenes%20rehan/24.png)

## 9. Auditoría Final de Hardening
Se finalizó el proceso con un escaneo de la herramienta **Lynis**, obteniendo un índice de hardening de **66** y validando los componentes de firewall y malware.

![Reglas de Entrada](../imagenes%20rehan/25.png)

## 10. Análisis de Vulnerabilidades y Sugerencias
Tras la auditoría con Lynis, se procedió a revisar las sugerencias específicas en el área de protección contra malware y configuración del firewall para seguir elevando el nivel de hardening.

![Reglas de Entrada](../imagenes%20rehan/26.png)

## 11. Configuración de Monitoreo de Integridad (Syscheck)
Se configuró el módulo **Syscheck** en el agente de Wazuh para realizar un seguimiento de cambios en archivos críticos. Se definieron rutas estratégicas como `/etc` y `/var/www/html` con una frecuencia de escaneo optimizada.

![Reglas de Entrada](../imagenes%20rehan/27.png)

## 12. Ajustes de Notificación y Logging
Se configuró el archivo de gestión de logs (`ossec.conf`) para asegurar que el sistema capture y envíe las alertas con el nivel de detalle necesario para la correlación de eventos en el servidor central.

![Reglas de Entrada](../imagenes%20rehan/28.png)

## 13. Sincronización de Configuración
Se reinició el servicio del agente de Wazuh para aplicar los cambios realizados en la monitorización de integridad y las nuevas políticas de logging.

![Reglas de Entrada](../imagenes%20rehan/29.png)

## 14. Acceso al Dashboard Centralizado
Se inició sesión en el panel de control de Wazuh para supervisar el estado global de la infraestructura y confirmar la recepción de datos de los agentes.

![Reglas de Entrada](../imagenes%20rehan/30.png)

## 15. Verificación de Agentes Activos
Se comprobó que el inventario de agentes reflejara los nodos correctamente conectados y sincronizados, garantizando que el servidor Ubuntu esté bajo supervisión constante.

![Reglas de Entrada](../imagenes%20rehan/31.png)

## 16. Visualización de Alertas de Seguridad
Acceso a la sección de eventos de seguridad, donde se clasifican los hallazgos por nivel de impacto, permitiendo una respuesta rápida ante posibles incidentes detectados.

![Reglas de Entrada](../imagenes%20rehan/32.png)

## 17. Análisis Geográfico de Amenazas
Utilización de mapas de calor para identificar el origen de las conexiones bloqueadas por el WAF, ayudando a identificar patrones de ataque desde regiones específicas.

![Reglas de Entrada](../imagenes%20rehan/33.png)

## 18. Dashboard de Eventos Web (ModSecurity)
Visualización gráfica de los ataques bloqueados por ModSecurity. El dashboard permite desglosar los intentos de inyección SQL y otros ataques de capa 7 detectados.

![Reglas de Entrada](../imagenes%20rehan/34.png)

## 19. Registro de Integridad de Archivos (FIM)
Detalle del inventario de cambios en archivos, donde el sistema registra cada modificación en el sistema de ficheros, crucial para detectar posibles persistencias o intrusiones.

![Reglas de Entrada](../imagenes%20rehan/35.png)



