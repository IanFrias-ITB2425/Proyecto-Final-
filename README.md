# 🛡️ CyberArena - Plataforma Híbrida de Entrenamiento (Red vs Blue Team)

![AWS](https://img.shields.io/badge/AWS-Academy-FF9900?style=for-the-badge&logo=amazonaws&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Integrado-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![pfSense](https://img.shields.io/badge/pfSense-Firewall-000000?style=for-the-badge&logo=pfsense&logoColor=white)
![Wazuh](https://img.shields.io/badge/Wazuh-SIEM-005E8C?style=for-the-badge&logo=wazuh&logoColor=white)

Bienvenido al repositorio oficial del Proyecto Final de Grado Superior (ASIXc2) del Institut Tecnològic de Barcelona.

## 📝 Descripción del Proyecto
**CyberArena** es un laboratorio interactivo de ciberseguridad diseñado bajo una arquitectura de **Nube Híbrida (Hybrid Cloud)**. Combina los recursos locales del instituto (**Nuvulet / IsardVDI**) con infraestructura pública en la nube (**AWS EC2**).

El objetivo de la plataforma es proporcionar un entorno controlado y fuertemente segmentado donde se puedan ejecutar ataques (Red Team) contra contenedores intencionadamente vulnerables, mientras un sistema automatizado (Blue Team) detecta, registra y alerta sobre las intrusiones en tiempo real.

## 🏗️ Arquitectura de la Infraestructura
El proyecto está dividido en dos zonas lógicas conectadas mediante un túnel VPN seguro:

1. **Zona Cloud (AWS Academy):**
   * **Servidor Web (Front-End):** Panel de control interactivo (Nginx, PHP, MariaDB).
   * **Servidor SOC (Blue Team):** Centro de Operaciones de Seguridad centralizado con **Wazuh Manager** para la recolección de logs y gestión de alertas vía API (Telegram).

2. **Zona On-Premise (Nuvulet / IsardVDI):**
   * **Gateway y Perímetro:** Firewall **pfSense** gestionando el enrutamiento, VLANs y el túnel VPN.
   * **Campo de Batalla (Red Team):** Servidores Ubuntu ejecutando contenedores Docker con vulnerabilidades web conocidas (OWASP Top 10) para simulaciones de ataque.

## 👥 Equipo y Roles (Responsabilidad Cruzada)
Siguiendo metodologías ágiles gestionadas a través de **ProofHub**, el equipo se distribuye de la siguiente manera:

* **Ian:** Arquitecto de Redes y Perímetro (pfSense, VPN, Enrutamiento).
* **Izan:** Ingeniero SOC y Monitorización (Wazuh, Suricata, Alertas).
* **Carlos:** Especialista en Sistemas y Automatización (Docker, Servicios Base, DNS).
* **Rehan:** Desarrollador Full-Stack y BBDD (Panel Web, Nginx, MariaDB).

> **Nota:** Todos los miembros del equipo aplican la política de *Responsabilidad Total*, participando activamente en el despliegue, documentación y comprensión transversal de toda la infraestructura.

## 📅 Planificación y Sprints
El desarrollo se estructura en 3 Sprints principales hasta la defensa a mediados de mayo:
* **Sprint 1 (13/4 - 24/4):** Despliegue de infraestructura base (AWS + Nuvulet), conectividad VPN y servicios core.
* **Sprint 2 (27/4 - 8/5):** Despliegue de contenedores vulnerables, configuración del SIEM y desarrollo del Dashboard dinámico.
* **Sprint 3 (11/5 - 15/5):** Pentesting (Red vs Blue), auditoría, congelación de código y redacción final de manuales.

## 📚 Documentación
La documentación completa (Memoria Técnica, Manual de Usuario y Manual de Administrador) se desarrollará de forma iterativa y estará referenciada en este repositorio al finalizar el proyecto.
