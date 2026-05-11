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

