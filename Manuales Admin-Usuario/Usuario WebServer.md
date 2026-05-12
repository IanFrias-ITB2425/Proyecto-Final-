# 👤 Manual de Usuario: Acceso a la Plataforma CyberArena
 
Este manual explica cómo acceder a la plataforma web CyberArena y al panel de control del servidor.
 
## 1. Requisitos Previos
 
- Tener conexión a Internet.
- Un navegador web moderno (Chrome, Firefox, Edge).
- Si el acceso es desde fuera de la red del proyecto, estar conectado a la VPN del laboratorio (consultar el Manual de Usuario de VPN).
## 2. Acceso a la Plataforma Web
 
La plataforma CyberArena es accesible públicamente a través de HTTPS. Abre el navegador y entra en:
 
```
https://cyberarena-admin.duckdns.org
```
 
> La conexión siempre va cifrada con TLS. Si el navegador muestra un aviso de certificado, significa que el certificado ha caducado y hay que renovarlo (ver sección del administrador).
 
## 3. Acceso al Dashboard de Control
 
El dashboard es una página de monitorización en tiempo real que muestra el estado de los servicios del servidor. Para acceder:
 
```
https://cyberarena-admin.duckdns.org/cyberarena_dashboard.php
```
 
Una vez dentro verás tres indicadores:
 
| Indicador | Qué significa |
|---|---|
| 🟢 `NOMINAL` / `ONLINE` / `ACTIVE` | El servicio funciona correctamente |
| 🔴 `OVERLOAD` / `CRITICAL` / `OFFLINE` | El servicio tiene un problema |
 
El ticker en la parte superior muestra en tiempo real la carga del servidor, el hostname, el estado del WAF y el agente de seguridad Wazuh.
 
> Esta página es de solo lectura. No permite realizar cambios en el servidor, únicamente consultar su estado.
 
## 4. Interpretación del Estado de los Servicios
 
**Web Server** — Muestra `NOMINAL` si la carga del sistema es normal. Si aparece `OVERLOAD`, el servidor está bajo mucha carga y puede responder lento.
 
**Database** — Muestra `ONLINE` si la base de datos MariaDB está accesible. Si aparece `CRITICAL`, la aplicación no puede conectarse a la base de datos.
 
**SIEM Telemetry** — Muestra `ACTIVE` si el agente de seguridad Wazuh está en funcionamiento y enviando eventos al servidor de monitorización.
 
## 5. Qué Hacer si Algo Falla
 
Si algún indicador aparece en rojo, contacta con el administrador del sistema. No intentes reiniciar servicios ni acceder al servidor por tu cuenta.
 
| Problema | A quién contactar |
|---|---|
| Página web no carga | Administrador del Web Server |
| Dashboard muestra `CRITICAL` en base de datos | Administrador del Web Server |
| Dashboard muestra `OFFLINE` en Wazuh | Administrador del Web Server |
| No puedes acceder desde fuera de la red | Administrador de Infraestructura (VPN) |
