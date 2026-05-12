# Guía de Usuario – CyberArena SOC

**Proyecto:** CyberArena – Plataforma de Ciberseguridad Centralizada  
**Acceso al Dashboard:** [https://wazuh-cyberarena.duckdns.org](https://wazuh-cyberarena.duckdns.org)

---

## ¿Qué es este sistema?

CyberArena es una plataforma de monitorización de seguridad centralizada (SOC). Permite visualizar en tiempo real los eventos de seguridad de los servidores bajo vigilancia, incluyendo intentos de intrusión, cambios en archivos y amenazas detectadas por antivirus.

---

## Acceso al Dashboard

### Credenciales

Para acceder al panel de control de Wazuh, necesitas las credenciales que te ha proporcionado el equipo de administración.

1. Abre tu navegador y ve a: **https://wazuh-cyberarena.duckdns.org**
2. Introduce tu usuario y contraseña.
3. Una vez dentro, verás el panel principal con el estado general del sistema.

> **Nota:** Si el navegador muestra una advertencia de certificado, puedes ignorarla de forma segura — el certificado SSL es válido y emitido por Let's Encrypt.

---

## Panel Principal

Al iniciar sesión verás el **Dashboard de Wazuh**, que muestra:

- **Estado de los agentes** — cuántos servidores están siendo monitorizados y si están activos.
- **Eventos recientes** — resumen de las últimas alertas de seguridad.
- **Gráficas en tiempo real** — intentos de ataque, escaneos de puertos y otras actividades sospechosas.

---

## Alertas en Discord

Cuando se detecta un **evento de nivel de riesgo 7 o superior**, recibirás una notificación automática en el canal **#alertas-wazuh** del servidor de Discord corporativo.

### ¿Qué información contiene una alerta?

Cada alerta incluye:
- **Nivel de riesgo** (de 1 a 15; cuanto más alto, más grave).
- **Descripción del evento** (p. ej., "Intento de fuerza bruta SSH detectado").
- **Servidor afectado** (Servidor-Web o Honeypots).
- **Fecha y hora** del incidente.

### ¿Qué debo hacer si recibo una alerta?

1. Lee la descripción del evento.
2. Evalúa el nivel de riesgo.
3. Si el nivel es **10 o superior**, notifica inmediatamente al equipo de administración.
4. Para más detalles, accede al dashboard de Wazuh en la sección **Threat Hunting**.

---

## Sección Threat Hunting

Esta sección te permite investigar eventos de seguridad en detalle.

1. En el menú lateral, busca **Threat Hunting**.
2. Filtra por servidor (Servidor-Web o Honeypots).
3. Verás una lista de alertas con su nivel, descripción y archivo afectado (si aplica).
4. Haz clic en cualquier alerta para obtener más información.

---

## Detección de Virus (VirusTotal)

El sistema está integrado con **VirusTotal**. Si alguien descarga o introduce un archivo malicioso conocido en los servidores monitorizados, el sistema lo detectará automáticamente y enviará una alerta tanto al dashboard como al canal de Discord.

> **Importante:** El sistema solo detecta malware conocido por la base de datos de VirusTotal. El equipo de administración puede detectar amenazas desconocidas por otros medios.

---

## Servidores Monitorizados

| Nombre | Descripción |
|--------|-------------|
| **Servidor-Web** | Servidor web principal de la plataforma. |
| **Honeypots** | Servidor señuelo con aplicaciones vulnerables para atraer y analizar atacantes. |

---

## Preguntas Frecuentes

**¿Qué hago si no puedo acceder al dashboard?**  
Contacta con el equipo de administración. El acceso requiere una conexión a internet normal; no se necesita VPN para el dashboard.

**¿Las alertas de Discord son siempre urgentes?**  
No todas. Las de nivel 7-9 son importantes pero no críticas. Las de nivel 10 o superior requieren atención inmediata.

**¿Puedo ver eventos antiguos?**  
Sí, desde el dashboard en la sección Threat Hunting puedes filtrar por fechas y revisar el historial completo.

---

*Para soporte técnico, contacta con el equipo de administración.*
