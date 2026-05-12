# 🛡️ Sprint 2 & 3 — Agente Wazuh y VPN WireGuard

En este sprint se conecta la máquina atacante al SIEM del equipo (Wazuh) y se configura el túnel VPN con WireGuard para poder trabajar desde casa.

---

## Sprint 2 — Agente Wazuh (SIEM)

### 1. Instalación

Se desinstala cualquier versión anterior y se instala el agente v4.9.2 directamente desde el paquete `.deb` oficial:

```bash
# Limpiar instalación anterior
sudo apt-get purge wazuh-agent -y
sudo rm -rf /var/ossec

# Descargar el paquete
wget https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent_4.9.2-1_amd64.deb

# Instalar con los parámetros del manager
sudo WAZUH_MANAGER='<IP_DEL_MANAGER>' \
     WAZUH_AGENT_GROUP='default' \
     WAZUH_AGENT_NAME='Honeypots' \
     dpkg -i ./wazuh-agent_4.9.2-1_amd64.deb
```

> **Captura — Descarga e instalación del paquete `.deb`:**
>
> ![wazuh install](screenshots/10-wazuh-install.png)

---

### 2. Activar y arrancar el servicio

```bash
sudo systemctl daemon-reload
```

---

### 3. Comprobación de alertas generadas

Con los contenedores del Sprint 1 activos y ejercicios en marcha, el agente Wazuh captura automáticamente los logs de los contenedores y los envía al manager. Desde el dashboard de Wazuh se pueden ver las alertas generadas por:

- Peticiones XSS en DVWA (`/vulnerabilities/xss_d/`)
- Intentos de SQL Injection (`/vulnerabilities/sqli_blind/`)
- Peticiones POST de Command Injection (`/vulnerabilities/exec/`)
- Intentos de Brute Force (`/vulnerabilities/brute/`)

```bash
# Ver logs del contenedor DVWA filtrados
sudo docker logs cyberarena_dvwa 2>&1 | grep "POST /vulnerabilities/exec/"
```

> **Captura — Log de POST a `/vulnerabilities/exec/` capturado:**
>
> ![exec log](screenshots/12-exec-log.png)

---

## Sprint 3 — Red VPN con WireGuard

### 1. Instalación

```bash
sudo apt-get install -y wireguard
```

---

### 2. Generación de claves criptográficas

```bash
cd /etc/wireguard
wg genkey | tee private.key | wg pubkey > public.key
```

---

### 3. Configuración del túnel (`wg0.conf`)

```ini
[Interface]
Address = 10.7.0.5/24
PrivateKey = <CLAVE_PRIVADA_GENERADA>

[Peer]
PublicKey = <CLAVE_PUBLICA_DE_IAN>
PresharedKey = <PRESHARED_KEY>
Endpoint = <IP_PUBLICA_IAN>:51820
AllowedIPs = 10.7.0.0/24, 10.0.1.0/24, 10.0.2.0/24
PersistentKeepalive = 25
```

> ⚠️ **Nota:** WireGuard se deja **desactivado por defecto** en el entorno del colegio (la máquina usa red interna directa). Solo se activa para sesiones en casa.

---

### 4. Uso

Para activar el túnel en casa:

```bash
sudo wg-quick up wg0
```

Para desactivarlo:

```bash
sudo wg-quick down wg0
```

---

## Resumen de IPs y servicios

| Concepto | Valor |
|----------|-------|
| IP máquina (red interna) | `10.0.2.239` |
| IP WireGuard asignada | `10.7.0.5/24` |
| Manager Wazuh (colegio) | `10.0.1.96` |
| Manager Wazuh (VPN) | `10.7.0.3` |
| Endpoint VPN Ian | `100.50.41.224:51820` |
