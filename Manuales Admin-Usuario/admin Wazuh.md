# Documentación Técnica para Administradores – SOC CyberArena

**Proyecto:** CyberArena – Plataforma de Ciberseguridad Centralizada  
**Proveedor Cloud:** Amazon Web Services (AWS)  
**Sistema Operativo Base:** Amazon Linux 2023 (Kernel 6.1)  
**Nodos Cliente:** Ubuntu 24.04 LTS  
**Fecha de despliegue:** Mayo 2026

---

## Índice

1. [Infraestructura AWS (EC2 – Wazuh Manager)](#1-infraestructura-aws)
2. [Security Groups (Firewall)](#2-security-groups)
3. [Conexión SSH al servidor](#3-conexión-ssh)
4. [Instalación de Wazuh SIEM](#4-instalación-wazuh)
5. [VPN WireGuard](#5-vpn-wireguard)
6. [Agentes de Seguridad](#6-agentes)
7. [Extracción de Logs Docker](#7-logs-docker)
8. [Motor de Integridad de Archivos (FIM)](#8-fim)
9. [Integración Discord (Webhooks)](#9-discord)
10. [Integración VirusTotal](#10-virustotal)
11. [Dominio Dinámico (Duck DNS) y SSL](#11-duckdns-ssl)
12. [Vinculación SSL en Wazuh Dashboard](#12-ssl-dashboard)
13. [Errores y Soluciones](#13-errores)

---

## 1. Infraestructura AWS

La instancia EC2 actúa como nodo maestro centralizador del SOC: procesa telemetría, indexa logs y coordina respuestas automatizadas.

### Especificaciones de la instancia

| Parámetro | Valor |
|-----------|-------|
| Tipo de instancia | t3.medium *(escalado a t3.large por recursos)* |
| Sistema operativo | Amazon Linux 2023 (AMI Oficial) |
| Estado | En ejecución ✅ |
| Arquitectura | x86_64 |
| Almacenamiento | EBS SSD 30 GB |

> **Incidente resuelto:** Durante la instalación inicial del indexador se produjo una congelación por falta de espacio y RAM en instancias con menos de 15 GB de disco. Se aplicó escalado vertical temporal a t3.large y se amplió el almacenamiento a 30 GB SSD.

---

## 2. Security Groups

El Security Group actúa como cortafuegos virtual a nivel de red, aplicando por defecto políticas restrictivas y controlando el tráfico inbound y outbound.

### Reglas de entrada (Inbound)

| Puerto | Protocolo | Uso |
|--------|-----------|-----|
| 22 | TCP | Acceso SSH |
| 443 | TCP | Dashboard web de Wazuh |
| 1514 | TCP | Envío de logs de agentes |
| 1515 | TCP | Registro de nuevos agentes |
| 55000 | TCP | API de Wazuh |

### Reglas de salida (Outbound)

- Todo el tráfico permitido: `0.0.0.0/0`

---

## 3. Conexión SSH

El acceso se realiza desde terminal local mediante SSH con autenticación por clave privada `.pem`.

```bash
# Asignar permisos estrictos a la clave privada
chmod 400 sockey.pem

# Conectar al servidor
ssh -i "claves-cyberarena.pem" ec2-user@3.229.242.100
```

---

## 4. Instalación de Wazuh SIEM

```bash
# Descargar e instalar Wazuh (instalación completa)
curl -sO https://packages.wazuh.com/4.14/wazuh-install.sh && sudo bash ./wazuh-install.sh -a
```

Al finalizar, el instalador proporciona las credenciales de acceso:

- **Usuario:** `admin`
- **Contraseña:** `M7xRS1KfhjYHlu?PSpB45NaT9UQ490Sb`

Verificación de acceso: [https://3.229.242.100/](https://3.229.242.100/)

---

## 5. VPN WireGuard

Para cifrar el tráfico de logs entre agentes y el manager, se desplegó una VPN ligera con **WireGuard**.

### Instalación

```bash
sudo dnf install wireguard-tools -y
```

### Generación de claves

```bash
wg genkey | tee privatekeywazuh | wg pubkey > publickeywazuh
```

### Configuración de la interfaz `/etc/wireguard/wg0.conf`

```ini
[Interface]
Address = 10.7.0.2/24
PrivateKey = eNR1/7urU7JNsHj32bOpgavewfK9+hqmq3IkmaLlLG0=
MTU = 1420

[Peer]
PublicKey = Xna/Ukx43zduP4algQ/IbeJTL/FWSbaMY6UkAadowlg=
Endpoint = 10.0.1.175:51820
AllowedIPs = 10.7.0.0/24
PersistentKeepalive = 25
```

> **Error resuelto:** `iptables-restore: command not found` — Amazon Linux 2023 no incluye iptables por defecto. Solución:
> ```bash
> sudo dnf install iptables-nft -y
> ```

---

## 6. Agentes de Seguridad

### Acceso a la configuración

En el dashboard de Wazuh: **Server Manager → Endpoints Summary → Deploy new agent**

### 6.1 Servidor Web (Ubuntu Desktop, DEB amd64)

**Server address:** `3.229.242.100`  
**Agent name:** `Servidor-Web`

```bash
# Descargar e instalar el agente
wget https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent_4.9.2-1_amd64.deb && \
sudo WAZUH_MANAGER='3.229.242.100' WAZUH_AGENT_NAME='Servidor-Web' dpkg -i ./wazuh-agent_4.9.2-1_amd64.deb

# Activar y arrancar el servicio
sudo systemctl daemon-reload
sudo systemctl enable wazuh-agent
sudo systemctl start wazuh-agent
```

### 6.2 Servidor Honeypot (Ubuntu, DEB amd64)

**Agent name:** `Honeypots`

```bash
# Descargar e instalar el agente
wget https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent_4.9.2-1_amd64.deb && \
sudo WAZUH_MANAGER='3.229.242.100' WAZUH_AGENT_NAME='Honeypots' dpkg -i ./wazuh-agent_4.9.2-1_amd64.deb

# Activar y arrancar el servicio
sudo systemctl daemon-reload
sudo systemctl enable wazuh-agent
sudo systemctl start wazuh-agent
```

---

## 7. Extracción de Logs Docker

El servidor Honeypots ejecuta **OWASP Juice Shop** y **DVWA** en contenedores Docker. Se usa un script extractor para canalizar los logs hacia el manager.

```bash
# Capturar logs de contenedores en tiempo real
sudo sh -c 'nohup docker logs -f cyberarena_juiceshop > /var/log/juiceshop.log 2>&1 &'
sudo sh -c 'nohup docker logs -f cyberarena_dvwa > /var/log/dvwa.log 2>&1 &'
```

Añadir al fichero `/var/ossec/etc/ossec.conf` del servidor Honeypot:

```xml
<localfile>
  <log_format>syslog</log_format>
  <location>/var/log/juiceshop.log</location>
</localfile>

<localfile>
  <log_format>syslog</log_format>
  <location>/var/log/dvwa.log</location>
</localfile>
```

---

## 8. Motor de Integridad de Archivos (FIM – Syscheck)

El módulo FIM usa `inotify` del kernel Linux para detectar cambios en tiempo real (hash SHA256).

### Configuración en Servidor-Web (`/var/ossec/etc/ossec.conf`)

```xml
<syscheck>
  <disabled>no</disabled>
  <frequency>43200</frequency>
  <scan_on_start>yes</scan_on_start>
  <alert_new_files>yes</alert_new_files>
  <directories realtime="yes">/tmp</directories>
  <directories realtime="yes">/var/www/html</directories>
</syscheck>
```

### Configuración en Servidor Honeypots (`/var/ossec/etc/ossec.conf`)

```xml
<syscheck>
  <disabled>no</disabled>
  <frequency>43200</frequency>
  <scan_on_start>yes</scan_on_start>
  <alert_new_files>yes</alert_new_files>
  <directories realtime="yes">/tmp</directories>
  <directories realtime="yes">/home/ubuntu</directories>
</syscheck>
```

Aplicar cambios:

```bash
sudo systemctl restart wazuh-agent
```

---

## 9. Integración Discord (Webhooks)

Las alertas de **nivel ≥ 7** se envían automáticamente al canal `#alertas-wazuh` de Discord.

### Configuración del Webhook

1. Crear canal `#alertas-wazuh` en Discord.
2. En configuración del canal → **Integraciones → Nuevo Webhook** → nombre: `Wazuh Bot`.
3. Copiar la URL del webhook.
4. Añadir el sufijo `/slack` al final de la URL (Discord es compatible con el formato Slack).

### Bloque en `/var/ossec/etc/ossec.conf` del Wazuh Manager

```xml
<integration>
  <name>slack</name>
  <level>7</level>
  <hook_url>https://discord.com/api/webhooks/[WEBHOOK_ID]/[WEBHOOK_TOKEN]/slack</hook_url>
  <alert_format>json</alert_format>
</integration>
```

> **Importante:** Usar el dominio `discord.com` (no `discordapp.com`) para evitar pérdida de payload en redirecciones.

---

## 10. Integración VirusTotal

Wazuh tiene integración nativa con VirusTotal para analizar archivos nuevos detectados por FIM.

### 10.1 Obtener API Key

Registrarse en [virustotal.com](https://virustotal.com) y copiar la API Key desde el perfil.

### 10.2 Configuración en Wazuh Manager

Añadir al fichero `/var/ossec/etc/ossec.conf`:

```xml
<virustotal>
  <enabled>yes</enabled>
  <api_key>TU_API_KEY_AQUI</api_key>
  <group>syscheck</group>
  <alert_format>json</alert_format>
</virustotal>
```

Reiniciar el manager:

```bash
sudo systemctl restart wazuh-manager
```

### 10.3 Configuración en agentes (Servidor-Web y Honeypots)

Editar `/var/ossec/etc/ossec.conf` y ampliar el bloque `<syscheck>`:

```xml
<syscheck>
  <disabled>no</disabled>
  <frequency>43200</frequency>
  <scan_on_start>yes</scan_on_start>
  <directories>/etc,/usr/bin,/usr/sbin</directories>
  <directories>/bin,/sbin,/boot</directories>
  <alert_new_files>yes</alert_new_files>
  <directories realtime="yes">/tmp</directories>
  <directories realtime="yes">/home/ubuntu</directories>
  <ignore>/etc/mtab</ignore>
  <ignore>/etc/hosts.deny</ignore>
  <ignore>/etc/mail/statistics</ignore>
  <ignore>/etc/random-seed</ignore>
  <ignore>/etc/random.seed</ignore>
  <ignore>/etc/adjtime</ignore>
</syscheck>
```

```bash
sudo systemctl restart wazuh-agent
```

### 10.4 Verificación

Descargar el archivo de prueba EICAR en el servidor Honeypots:

```bash
wget -P /tmp https://secure.eicar.org/eicar.com
```

Se debe recibir alerta nivel 12 en Discord y en Wazuh (Threat Hunting → Honeypots).

---

## 11. Dominio Dinámico (Duck DNS) y Certificado SSL

### 11.1 Registro en Duck DNS

1. Registrarse en [duckdns.org](https://duckdns.org) con cuenta de Google.
2. Crear el subdominio: `wazuh-cyberarena`.
3. Asignar la IP pública del servidor EC2.

### 11.2 Instalar Certbot y obtener certificado SSL

```bash
# Instalar Certbot
sudo dnf install certbot -y

# Solicitar certificado SSL (puerto 80 debe estar libre)
sudo certbot certonly --standalone -d wazuh-cyberarena.duckdns.org
```

Durante el proceso: introducir correo electrónico, aceptar condiciones, denegar publicidad.

Las claves se guardan en `/etc/letsencrypt/live/wazuh-cyberarena.duckdns.org/`.

---

## 12. Vinculación SSL en Wazuh Dashboard

### Permisos sobre certificados

```bash
sudo chown -R root:wazuh-dashboard /etc/letsencrypt/
sudo chmod -R 750 /etc/letsencrypt/
```

### Configuración de OpenSearch Dashboards

Editar `/etc/wazuh-dashboard/opensearch_dashboards.yml`:

```yaml
server.host: 0.0.0.0
opensearch.hosts: https://127.0.0.1:9200
server.port: 443
opensearch.ssl.verificationMode: certificate
opensearch.requestHeadersAllowlist: ["securitytenant","Authorization"]
opensearch_security.multitenancy.enabled: false
opensearch_security.readonly_mode.roles: ["kibana_read_only"]
server.ssl.enabled: true
server.ssl.key: "/etc/letsencrypt/live/wazuh-cyberarena.duckdns.org/privkey.pem"
server.ssl.certificate: "/etc/letsencrypt/live/wazuh-cyberarena.duckdns.org/fullchain.pem"
opensearch.ssl.certificateAuthorities: ["/etc/wazuh-dashboard/certs/root-ca.pem"]
uiSettings.overrides.defaultRoute: /app/wz-home
opensearch_security.cookie.secure: true
```

Reiniciar el dashboard:

```bash
sudo systemctl restart wazuh-dashboard
```

Acceso final: **https://wazuh-cyberarena.duckdns.org**

---

## 13. Errores y Soluciones

### 13.1 Error SSL en entorno Python aislado de Wazuh

**Síntoma:** El script de integración con Discord no podía verificar el certificado SSL.

**Solución:** Editar `/var/ossec/integrations/slack.py` y añadir al inicio:

```python
import ssl
ssl._create_default_https_context = ssl._create_unverified_context
```

### 13.2 Restricción de permisos UNIX

**Síntoma:** Las alertas se registraban en `/var/ossec/logs/alerts/alerts.json` pero no se enviaban a Discord.

**Solución:**

```bash
sudo chmod +x /var/ossec/integrations/slack
```

### 13.3 Redirección HTTP con pérdida de Payload

**Síntoma:** El script devolvía `200 OK` pero no llegaban mensajes a Discord.

**Causa:** La URL usaba `discordapp.com` en lugar de `discord.com`. La redirección HTTP convertía el `POST` en `GET`, destruyendo el payload.

**Solución:** Cambiar la URL del webhook en `ossec.conf` para que apunte directamente a `discord.com`.

### 13.4 HTTP 400 Bad Request – Timestamp incompatible

**Síntoma:** Discord rechazaba las peticiones con error `400 Bad Request` durante ataques reales.

**Causa:** El campo `ts` (timestamp) en Wazuh era un string decimal (ej: `1223344.45455435`), pero la API de Discord requiere un Integer.

**Solución:** Editar `/var/ossec/integrations/slack.py` y cambiar:

```python
# Antes
msg['ts'] = alert['id']

# Después
msg['ts'] = int(alert['id'].split('.')[0])
```

| Código | Descripción | Ejemplo |
|--------|-------------|---------|
| `alert['id']` | ID de alerta con decimales | `"1778360885.149374"` |
| `.split('.')` | Corta por el punto | `["1778360885", "149374"]` |
| `[0]` | Toma la parte entera | `"1778360885"` |
| `int(...)` | Convierte a Integer | `1778360885` |

---

*Documentación generada para uso interno del equipo técnico de CyberArena.*
