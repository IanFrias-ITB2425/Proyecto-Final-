# Reporte de Implementación de Seguridad y Hardening

Este documento detalla el proceso de instalación, configuración y validación de las medidas de seguridad aplicadas en el servidor Ubuntu 24.04, incluyendo un Firewall de Aplicaciones Web (WAF), monitoreo centralizado y auditoría de sistema.

---

## 1. Instalación de ModSecurity (WAF)
Se inició con la actualización de los repositorios y la instalación del módulo ModSecurity para fortalecer la capa de aplicación del servidor web.

![Reglas de Entrada](../imagenes%20rehan/17.png)

## 2. Configuración del Motor de Reglas
Para que el WAF sea efectivo, se cambió la directiva `SecRuleEngine` de `DetectionOnly` a `On`. Esto permite que el sistema bloquee activamente las peticiones maliciosas en lugar de solo registrarlas.

![Reglas de Entrada](../imagenes%20rehan/18.png)

## 3. Implementación del Core Rule Set (CRS) de OWASP
Se descargó e instaló el conjunto de reglas base de OWASP (v3.3.5-2), que proporciona protección contra las vulnerabilidades web más críticas (Inyección, XSS, etc.).

![Reglas de Entrada](../imagenes%20rehan/19.png)

## 4. Configuración y Carga de Reglas
Se editaron los archivos de configuración para integrar el motor de OWASP y las reglas de ataque, además de prever un archivo de exclusiones (whitelist) para optimizar el tráfico.

![Reglas de Entrada](../imagenes%20rehan/20.png)

## 5. Monitoreo de Eventos en Tiempo Real (Logs)
El análisis de los logs del sistema confirma la detección de ataques de inyección SQL (SQLi), mostrando detalles técnicos como el ID de la regla activada y la IP del atacante.

![Reglas de Entrada](../imagenes%20rehan/21.png)

## 6. Pruebas de Penetración y Validación de Respuesta
Se realizaron peticiones mediante `curl` para validar la efectividad del WAF:
- **Intento Malicioso:** Resultado **403 Forbidden** (Bloqueado con éxito).
- **Tráfico Legítimo:** Resultado **200 OK** (Acceso permitido).


![Reglas de Entrada](../imagenes%20rehan/22.png)

## 7. Validación de la Configuración del Servicio
Se ejecutó un test de sintaxis en el servidor web (Nginx) para confirmar que las nuevas reglas y el módulo de seguridad estuvieran correctamente cargados sin errores.


![Reglas de Entrada](../imagenes%20rehan/23.png)

## 8. Configuración de Monitoreo Centralizado (Wazuh)
Se configuró el agente de Wazuh editando el archivo `ossec.conf` para reportar todos los eventos de seguridad y logs del sistema al servidor central en la IP `10.0.1.96`.

![Reglas de Entrada](../imagenes%20rehan/24.png)

## 9. Auditoría Final con Lynis
Para validar el estado general de endurecimiento (hardening) del servidor, se ejecutó un escaneo con Lynis, obteniendo un **Hardening Index de 66** tras realizar 266 pruebas.

![Reglas de Entrada](../imagenes%20rehan/25.png)

---
**Estado del Sistema:** Protegido y Monitoreado.
