# Reporte de Implementación de Seguridad y Hardening

Este documento detalla el proceso de instalación, configuración y validación de las medidas de seguridad aplicadas en el servidor Ubuntu 24.04, incluyendo un Firewall de Aplicaciones Web (WAF), monitoreo centralizado y auditoría de sistema.

---

## 1. Instalación de ModSecurity (WAF)
Se inició con la actualización de los repositorios y la instalación del módulo ModSecurity para fortalecer la capa de aplicación del servidor web.

![Reglas de Entrada](../imagenes%20rehan/17.png)

## 2. Configuración del Motor de Reglas
Para que el WAF sea efectivo, se cambió la directiva `SecRuleEngine` de `DetectionOnly` a `On`. Esto permite que el sistema bloquee activamente las peticiones maliciosas en lugar de solo registrarlas.

![Reglas de Entrada](../imagenes%20rehan/18.png)

## 3. Despliegue de OWASP Core Rule Set (CRS)
Se instaló el conjunto de reglas básicas de OWASP (CRS), el cual proporciona protecciones genéricas contra ataques como SQLi, XSS y otras vulnerabilidades del Top 10 de OWASP.

![Reglas de Entrada](../imagenes%20rehan/19.png)

## 4. Integración de Reglas en la Configuración
Se configuró el archivo `modsecurity.conf` para incluir el "cerebro" de OWASP y las reglas de ataque, además de preparar un archivo de whitelist para gestionar excepciones.

![Reglas de Entrada](../imagenes%20rehan/22.png)

## 5. Verificación de la Configuración
Antes de reiniciar el servicio, se ejecutó una prueba de sintaxis en Nginx para asegurar que la integración del módulo ModSecurity fuera correcta y no afectara la disponibilidad del servicio.

![Reglas de Entrada](../imagenes%20rehan/21.png)

## 6. Pruebas de Ataque y Validación (Bloqueo 403)
Se realizaron pruebas de inyección SQL mediante `curl`.
- **Intento de ataque:** Se recibió un código **403 Forbidden**, confirmando que el WAF está funcionando.
- **Acceso legítimo:** La página principal cargó con un código **200 OK**.

![Reglas de Entrada](../imagenes%20rehan/20.png)

## 7. Análisis de Logs de Seguridad
El archivo de errores muestra detalladamente los eventos de bloqueo. Se observa la detección de "SQL Injection Attack" con severidad crítica, activada por la regla `942100`.

![Reglas de Entrada](../imagenes%20rehan/23.png)

## 8. Configuración de Monitoreo Centralizado (Wazuh)
Se configuró el agente de Wazuh editando el archivo `ossec.conf` para reportar todos los eventos de seguridad y logs del sistema al servidor central en la IP `10.0.1.96`.

![Reglas de Entrada](../imagenes%20rehan/24.png)

## 9. Auditoría Final con Lynis
Para validar el estado general de endurecimiento (hardening) del servidor, se ejecutó un escaneo con Lynis, obteniendo un **Hardening Index de 66** tras realizar 266 pruebas.

![Reglas de Entrada](../imagenes%20rehan/25.png)

---
**Estado del Sistema:** Protegido y Monitoreado.
