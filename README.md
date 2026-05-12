# 🛡️ CyberArena - Ecosistema de Ciberseguridad Defensiva en AWS Cloud

![AWS](https://img.shields.io/badge/AWS-Academy-FF9900?style=for-the-badge&logo=amazonaws&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Honeypot-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![ModSecurity](https://img.shields.io/badge/ModSecurity-WAF-E32028?style=for-the-badge&logo=apache&logoColor=white)
![Wazuh](https://img.shields.io/badge/Wazuh-SIEM-005E8C?style=for-the-badge&logo=wazuh&logoColor=white)
![SSH](https://img.shields.io/badge/SSH-Tunneling-4EAA25?style=for-the-badge&logo=gnubash&logoColor=white)

## 📝 Descripción del Proyecto
**CyberArena** es una infraestructura avanzada de ciberseguridad defensiva desplegada íntegramente sobre **AWS Cloud**. El proyecto aplica los principios de **Zero-Trust y Defensa en Profundidad**, segmentando los activos en subredes públicas y privadas dentro de una VPC.

El objetivo de la plataforma es demostrar un ciclo completo de seguridad: prevención perimetral mediante WAF, contención mediante plataformas de engaño (Honeypots), y detección en tiempo real a través de un SOC centralizado. Además, el proyecto destaca por su **resiliencia técnica**, habiendo sustituido arquitecturas VPN bloqueadas por cortafuegos corporativos mediante el uso avanzado de túneles SSH dinámicos (`Local Port Forwarding` y `ProxyJump`).

## 🏗️ Arquitectura de la Infraestructura
El entorno está diseñado en AWS aislando lógicamente los recursos según su criticidad:

1. **Zona Pública (DMZ):**
   * **Bastion Host / Gateway:** Servidor de salto seguro. Único punto de entrada administrativo autorizado para acceder a la red interna mediante túneles SSH cifrados.
   * **Servidor de Producción (Front-End):** Servidor Apache endurecido y protegido por un Web Application Firewall (**ModSecurity**) con reglas OWASP, accesible públicamente vía DNS dinámico (`cyberarena-admin.duckdns.org`).

2. **Zona Privada (El Búnker):**
   * **Honeypot de Alta Interacción (Red Team):** Servidor sin salida directa a Internet que ejecuta contenedores Docker con aplicaciones web intencionadamente vulnerables (**DVWA y OWASP Juice Shop**). Actúa como cebo para capturar TTPs (Tácticas, Técnicas y Procedimientos) de posibles atacantes que hayan eludido el perímetro.

3. **Zona de Gestión y Monitorización:**
   * **Centro de Operaciones de Seguridad (SOC):** Instancia dedicada ejecutando **Wazuh (SIEM/XDR)**. Recolecta, correlaciona y alerta en tiempo real sobre bloqueos del WAF, intentos de fuerza bruta y movimientos laterales registrados en los contenedores.

## 👥 Equipo y Roles (Responsabilidad Cruzada)
* **Ian (Arquitecto de Sistemas):** Diseño de la VPC, gestión de Security Groups, enrutamiento y resiliencia de conectividad (Bastion Host y SSH Tunneling).
* **Rehan (Defense Engineer):** Despliegue del servidor de producción, integración de DuckDNS, configuración de Apache y gestión de políticas de ModSecurity (WAF).
* **Carlos (Red Team & Honeypot):** Despliegue del entorno de engaño con Docker, auditoría ofensiva y ejecución de inyecciones (SQLi, XSS, Command Injection) para validar el SOC.
* **Izan (SOC Analyst):** Configuración del SIEM Wazuh, despliegue de agentes en toda la topología y monitorización/correlación de incidentes de seguridad.

> **Nota:** Todos los miembros del equipo aplican la política de *Responsabilidad Total*, participando activamente en el despliegue, documentación y comprensión transversal de toda la infraestructura.

## 📅 Planificación y Sprints (Evolución del Proyecto)
El desarrollo se ha estructurado en fases iterativas adaptadas a las necesidades y retos de la infraestructura:
* **Sprint 1 (Fase de Diseño y Core):** Diseño de la arquitectura en AWS (VPC, Subredes), despliegue de instancias base de Ubuntu, configuración de Security Groups y resolución de nombres dinámicos (DuckDNS).
* **Sprints 2 & 3 (Fase de Integración, SOC y Auditoría):** Fusión de las fases finales de despliegue y pruebas. Endurecimiento del perímetro (WAF ModSecurity), despliegue de contenedores vulnerables (Honeypot) y **pivote técnico** hacia túneles SSH de Nivel 3. Implementación total del SOC (Wazuh), ejecución del Pentesting cruzado (Red vs Blue), extracción de telemetría forense y preparación de la demostración práctica.

## 📚 Documentación
La documentación completa (Memoria Técnica, Diagramas de Topología, y Manuales de Despliegue) refleja el estado final de producción y se encuentra estructurada en el repositorio. Todo el código fuente de configuración, scripts de automatización y evidencias de logs de intrusión (Prue
