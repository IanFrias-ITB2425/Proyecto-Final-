# Informe de Auditoría de Seguridad – Lynis 3.0.9
 
> **Herramienta:** Lynis 3.0.9 – CISOfy  
> **Fecha de auditoría:** 11 de mayo de 2026  
> **Host auditado:** `ip-10-0-1-225`  
> **Sistema operativo:** Ubuntu 24.04 LTS  
> **Kernel:** 6.17.0 · x86_64  
> **Perfil aplicado:** `/etc/lynis/default.prf`  
> **Categoría de test:** Completa (all)
 
---
 
## Índice
 
1. [Resultado Global](#1-resultado-global)
2. [Entorno del Sistema](#2-entorno-del-sistema)
3. [Plugins y Tests de Debian](#3-plugins-y-tests-de-debian)
4. [Arranque y Servicios](#4-arranque-y-servicios)
5. [Kernel](#5-kernel)
6. [Memoria y Procesos](#6-memoria-y-procesos)
7. [Usuarios, Grupos y Autenticación](#7-usuarios-grupos-y-autenticación)
8. [Sistemas de Ficheros](#8-sistemas-de-ficheros)
9. [Red y Protocolos](#9-red-y-protocolos)
10. [Servidor Web (Nginx)](#10-servidor-web-nginx)
11. [SSH](#11-ssh)
12. [Base de Datos](#12-base-de-datos)
13. [Firewall](#13-firewall)
14. [Logging y Auditoría](#14-logging-y-auditoría)
15. [Frameworks de Seguridad](#15-frameworks-de-seguridad)
16. [Integridad de Ficheros](#16-integridad-de-ficheros)
17. [Hardening del Kernel (sysctl)](#17-hardening-del-kernel-sysctl)
18. [Permisos de Ficheros](#18-permisos-de-ficheros)
19. [Advertencias Detectadas](#19-advertencias-detectadas)
20. [Sugerencias de Mejora](#20-sugerencias-de-mejora)
21. [Plan de Remediación Priorizado](#21-plan-de-remediación-priorizado)
---
 
## 1. Resultado Global
 
| Métrica | Valor |
|---|---|
| **Hardening Index** | **73 / 100** |
| Tests realizados | 267 |
| Plugins habilitados | 1 |
| Advertencias | 2 |
| Sugerencias | 41 |
| Modo de escaneo | Normal |
 
```
Hardening index : 73 [##############      ]
```
 
**Módulos evaluados:**
 
| Módulo | Estado |
|---|---|
| Security audit | ✅ Activo |
| Vulnerability scan | ✅ Activo |
| Compliance status | ❓ No configurado |
| Firewall | ✅ Detectado |
| Malware scanner | ✅ Detectado |
 
> Un índice de 73 sobre 100 indica una postura de seguridad razonable, con margen de mejora especialmente en hardening de servicios, logging externo e integridad de ficheros. Las secciones siguientes detallan cada área evaluada.
 
---
 
## 2. Entorno del Sistema
 
| Parámetro | Valor |
|---|---|
| Versión de Lynis | 3.0.9 |
| Sistema operativo | Linux – Ubuntu 24.04 |
| Versión del kernel | 6.17.0 |
| Arquitectura | x86_64 |
| Hostname | `ip-10-0-1-225` |
| Perfil de configuración | `/etc/lynis/default.prf` |
| Log de auditoría | `/var/log/lynis.log` |
| Reporte de datos | `/var/log/lynis-report.dat` |
| Actualización disponible | NO (versión al día) |
 
---
 
## 3. Plugins y Tests de Debian
 
### Directorios del sistema
Todos los directorios binarios esenciales verificados correctamente:
 
| Directorio | Estado |
|---|---|
| `/bin` | ✅ FOUND |
| `/sbin` | ✅ FOUND |
| `/usr/bin` | ✅ FOUND |
| `/usr/sbin` | ✅ FOUND |
| `/usr/local/bin` | ✅ FOUND |
| `/usr/local/sbin` | ✅ FOUND |
 
### Autenticación PAM
| Módulo | Estado |
|---|---|
| `libpam-tmpdir` | ✅ Instalado y habilitado |
 
### Cifrado de sistemas de ficheros
Ninguna partición está cifrada. En un entorno de producción con datos sensibles, se recomienda evaluar el cifrado de volúmenes con LUKS/dm-crypt.
 
| Partición | Estado |
|---|---|
| `/` (nvme0n1p1) | ⚠️ NO CIFRADA |
| `/boot` (nvme0n1p16) | ⚠️ NO CIFRADA |
| `/boot/efi` (nvme0n1p15) | ⚠️ NO CIFRADA |
| Snaps de sistema | ⚠️ NO CIFRADOS |
 
### Software de seguridad Debian
| Paquete | Estado | Observación |
|---|---|---|
| `apt-listbugs` | ❌ No instalado | Recomendado para alertas críticas pre-instalación |
| `apt-listchanges` | ❌ No instalado | Recomendado para control de cambios en upgrades |
| `needrestart` | ✅ Instalado | Detecta servicios que requieren reinicio tras actualización |
| `fail2ban` | ✅ Instalado con `jail.local` | Protección activa contra fuerza bruta |
 
---
 
## 4. Arranque y Servicios
 
| Verificación | Resultado |
|---|---|
| Gestor de servicios | systemd |
| UEFI boot | ✅ HABILITADO |
| Secure Boot | ⚠️ DESHABILITADO |
| GRUB2 | ✅ Presente |
| Contraseña en GRUB | ❌ NO CONFIGURADA |
| Servicios en ejecución | 29 |
| Servicios habilitados al arranque | 56 |
| Permisos de archivos de arranque | ✅ OK |
 
> **Riesgo:** La ausencia de contraseña en GRUB permite modificar parámetros de arranque (como el modo usuario único) sin autenticación. Se recomienda configurar una contraseña de GRUB en entornos de alta seguridad.
 
### Análisis de seguridad de servicios (`systemd-analyze security`)
 
La mayoría de servicios del sistema carecen de sandboxing a nivel de systemd. A continuación se clasifican por nivel de exposición:
 
**🔴 UNSAFE** (sin sandboxing):
 
`acpid`, `cron`, `dbus`, `fail2ban`, `nginx`, `mariadb`, `php8.3-fpm`, `postfix`, `rsyslog` (MEDIUM), `ssh`, `wazuh-agent`, `snapd`, `unattended-upgrades` y otros servicios del sistema.
 
**🟡 MEDIUM:**
 
| Servicio | Nivel |
|---|---|
| `ModemManager.service` | MEDIUM |
| `rsyslog.service` | MEDIUM |
| `systemd-udevd.service` | MEDIUM |
| `uuidd.service` | MEDIUM |
 
**🟠 EXPOSED:**
 
| Servicio | Nivel |
|---|---|
| `irqbalance.service` | EXPOSED |
| `mariadb.service` | EXPOSED |
 
**🟢 PROTECTED:**
 
| Servicio | Nivel |
|---|---|
| `chrony.service` | PROTECTED |
| `polkit.service` | PROTECTED |
| `systemd-journald.service` | PROTECTED |
| `systemd-logind.service` | PROTECTED |
| `systemd-networkd.service` | PROTECTED |
| `systemd-resolved.service` | PROTECTED |
 
> Se recomienda añadir directivas de hardening (`PrivateTmp`, `NoNewPrivileges`, `ProtectSystem`, etc.) a los servicios críticos como `nginx`, `mariadb` y `ssh` para reducir el impacto en caso de compromiso.
 
---
 
## 5. Kernel
 
| Verificación | Resultado |
|---|---|
| Run level por defecto | RUNLEVEL 5 |
| Soporte PAE/NX | ✅ FOUND |
| Módulos del kernel cargados | 36 activos |
| Fichero de configuración del kernel | ✅ FOUND |
| Actualización de kernel disponible | ✅ NO requerida |
| Core dumps (systemd) | DEFAULT (sin deshabilitar) |
| Core dumps setuid | ✅ PROTECTED |
| Reinicio necesario | ✅ NO |
 
---
 
## 6. Memoria y Procesos
 
| Verificación | Resultado |
|---|---|
| `/proc/meminfo` | ✅ FOUND |
| Procesos zombie/muertos | ✅ NO ENCONTRADOS |
| Procesos en espera de I/O | ✅ NO ENCONTRADOS |
| Herramienta prelink | ✅ NO ENCONTRADA (correcto) |
 
> El sistema presenta un estado limpio de procesos. La ausencia de procesos zombie y de prelink es positiva desde el punto de vista de seguridad.
 
---
 
## 7. Usuarios, Grupos y Autenticación
 
| Verificación | Resultado |
|---|---|
| Cuentas de administrador | ✅ OK |
| UIDs únicos | ✅ OK |
| Consistencia de grupos (`grpck`) | ✅ OK |
| IDs de grupo únicos | ✅ OK |
| Nombres de grupo únicos | ✅ OK |
| Consistencia del fichero de contraseñas | ✅ OK |
| Método de hash de contraseñas | ✅ OK |
| Rondas de hash (mínimo) | ✅ CONFIGURADO |
| Autenticación NIS/NIS+ | ✅ NO HABILITADA |
| Archivos sudoers y permisos | ✅ OK |
| Fortaleza de contraseñas PAM | ⚠️ SUGGESTION |
| Cuentas sin fecha de expiración | ✅ OK |
| Cuentas sin contraseña | ✅ OK |
| Cuentas bloqueadas | ⚠️ FOUND (revisar) |
| Envejecimiento de contraseñas (mín/máx) | ✅ CONFIGURADO |
| Contraseñas expiradas | ✅ OK |
| Modo usuario único con autenticación | ✅ OK |
| umask en `/etc/login.defs` | ⚠️ SUGGESTION (027 recomendado) |
| Registro de intentos fallidos de login | ✅ HABILITADO |
 
---
 
## 8. Sistemas de Ficheros
 
| Verificación | Resultado |
|---|---|
| Punto de montaje `/home` | ⚠️ SUGGESTION (partición separada recomendada) |
| Punto de montaje `/tmp` | ⚠️ SUGGESTION (partición separada recomendada) |
| Punto de montaje `/var` | ⚠️ SUGGESTION (partición separada recomendada) |
| `/proc` con `hidepid` | ⚠️ SUGGESTION |
| Archivos antiguos en `/tmp` | ✅ OK |
| Sticky bit en `/tmp` | ✅ OK |
| Sticky bit en `/var/tmp` | ✅ OK |
| Soporte ACL en sistema raíz | ✅ HABILITADO |
| Opciones de montaje `/dev` | ✅ HARDENED |
| Opciones de montaje `/run` | ✅ HARDENED |
| Opciones de montaje `/dev/shm` | 🟡 PARTIALLY HARDENED |
 
**Resumen de opciones de montaje restrictivas:**
 
| Opción | Particiones sin ella |
|---|---|
| `nodev` | 6 particiones |
| `noexec` | 11 particiones |
| `nosuid` | 7 particiones |
 
### Dispositivos USB
| Verificación | Resultado |
|---|---|
| Driver `usb-storage` | ⚠️ NO DESHABILITADO |
| Autorización de dispositivos USB | ✅ DESHABILITADA |
| USBGuard | ❌ NO ENCONTRADO |
 
---
 
## 9. Red y Protocolos
 
| Verificación | Resultado |
|---|---|
| IPv6 | ✅ HABILITADO (modo AUTO) |
| Nameserver (`127.0.0.53`) | ✅ OK |
| DNSSEC (systemd-resolved) | ❓ UNKNOWN |
| Interfaces promiscuas | ✅ OK |
| Monitorización ARP | ❌ NO ENCONTRADA |
| Protocolos de red poco comunes | ✅ 0 detectados |
| Protocolos a evaluar | `dccp`, `sctp`, `rds`, `tipc` |
 
> Se recomienda deshabilitar mediante módulos del kernel los protocolos `dccp`, `sctp`, `rds` y `tipc` si no son requeridos por la aplicación, añadiéndolos a `/etc/modprobe.d/blacklist.conf`.
 
---
 
## 10. Servidor Web (Nginx)
 
| Verificación | Resultado |
|---|---|
| Nginx detectado | ✅ FOUND |
| Fichero de configuración | ✅ FOUND |
| Includes de configuración | 4 encontrados |
| SSL configurado | ✅ SÍ |
| Ciphers explícitamente configurados | ⚠️ NO |
| Preferencia de ciphers del servidor | ✅ SÍ |
| Protocolos SSL configurados | ✅ SÍ |
| **Protocolos inseguros detectados** | ❌ SÍ (requiere corrección) |
| Log de acceso | ✅ Configurado |
| Log de errores | ✅ Configurado |
| Modo debug en error_log | ✅ NO |
 
**Módulos de Nginx cargados:**
- `/etc/nginx/modules-enabled/10-mod-http-ndk.conf`
- `/etc/nginx/modules-enabled/50-mod-http-modsecurity.conf` ← ModSecurity activo
> **Acción requerida:** Deshabilitar protocolos SSL/TLS inseguros (TLSv1.0, TLSv1.1) en la configuración de Nginx y definir explícitamente una suite de ciphers seguros. Consultar [HTTP-6710](https://cisofy.com/lynis/controls/HTTP-6710/).
 
---
 
## 11. SSH
 
Daemon SSH activo y configurado. Estado de cada opción evaluada:
 
| Opción OpenSSH | Estado | Valor actual | Recomendado |
|---|---|---|---|
| `AllowTcpForwarding` | ✅ OK | no | – |
| `ClientAliveCountMax` | ⚠️ SUGGESTION | 3 | 2 |
| `ClientAliveInterval` | ✅ OK | – | – |
| `FingerprintHash` | ✅ OK | – | – |
| `GatewayPorts` | ✅ OK | – | – |
| `IgnoreRhosts` | ✅ OK | – | – |
| `LoginGraceTime` | ✅ OK | – | – |
| `LogLevel` | ✅ OK | VERBOSE | – |
| `MaxAuthTries` | ✅ OK | 3 | – |
| `MaxSessions` | ⚠️ SUGGESTION | 10 | 2 |
| `PermitRootLogin` | ✅ OK | – | – |
| `PermitUserEnvironment` | ✅ OK | – | – |
| `PermitTunnel` | ✅ OK | – | – |
| `Port` | ⚠️ SUGGESTION | 22 | Puerto no estándar |
| `PrintLastLog` | ✅ OK | – | – |
| `StrictModes` | ✅ OK | – | – |
| `TCPKeepAlive` | ⚠️ SUGGESTION | YES | NO |
| `UseDNS` | ✅ OK | – | – |
| `X11Forwarding` | ✅ OK | no | – |
| `AllowAgentForwarding` | ⚠️ SUGGESTION | YES | NO |
| `AllowUsers` | ❌ NOT FOUND | – | Configurar whitelist |
| `AllowGroups` | ❌ NOT FOUND | – | Configurar whitelist |
 
> Las opciones correctamente configuradas reflejan el trabajo de hardening previo. Las sugerencias restantes (`ClientAliveCountMax`, `MaxSessions`, `TCPKeepAlive`, `AllowAgentForwarding`, `AllowUsers`/`AllowGroups`) son mejoras adicionales de bajo esfuerzo y alto impacto.
 
---
 
## 12. Base de Datos
 
| Verificación | Resultado |
|---|---|
| Proceso MySQL/MariaDB | ✅ FOUND (en ejecución) |
 
---
 
## 13. Firewall
 
| Verificación | Resultado |
|---|---|
| Módulo `iptables` en kernel | ✅ FOUND |
| Políticas de cadenas iptables | ✅ FOUND |
| Ruleset vacío | ⚠️ **WARNING** – Módulo cargado sin reglas activas |
| Reglas sin uso | ✅ OK |
| Firewall basado en host | ✅ ACTIVO |
 
> **Advertencia activa [FIRE-4512]:** El módulo iptables está cargado pero no hay reglas definidas. Si la protección de firewall recae únicamente en el Security Group de AWS, se recomienda documentarlo explícitamente y considerar añadir reglas locales con `ufw` o `iptables` como capa de defensa adicional.
 
---
 
## 14. Logging y Auditoría
 
| Verificación | Resultado |
|---|---|
| Daemon de logging activo | ✅ OK |
| systemd journal | ✅ FOUND |
| RSyslog | ✅ FOUND |
| Rotación de logs (`logrotate`) | ✅ OK |
| Logging remoto | ❌ NO HABILITADO |
| Archivos de log eliminados en uso | ⚠️ FILES FOUND |
| `auditd` | ❌ NO ENCONTRADO |
| Accounting del sistema (`sysstat`) | ❌ DESHABILITADO |
| Process accounting | ❌ NO ENCONTRADO |
| NTP / sincronización de tiempo | ✅ `chronyd` activo |
 
> **Punto crítico:** La ausencia de logging remoto implica que si el sistema se ve comprometido, los logs locales pueden ser alterados o eliminados. Se recomienda configurar el envío de logs a un servidor centralizado (SIEM Wazuh ya presente) mediante RSyslog o el agente Wazuh.
 
---
 
## 15. Frameworks de Seguridad
 
| Framework | Estado |
|---|---|
| AppArmor | ✅ HABILITADO (50 procesos sin confinar) |
| SELinux | ❌ NO ENCONTRADO |
| TOMOYO Linux | ❌ NO ENCONTRADO |
| grsecurity | ❌ NO ENCONTRADO |
| MAC framework implementado | ✅ OK |
 
> AppArmor está activo pero 50 procesos corren sin perfil de confinamiento. Se recomienda revisar qué procesos críticos pueden beneficiarse de perfiles AppArmor adicionales.
 
---
 
## 16. Integridad de Ficheros
 
| Herramienta | Estado |
|---|---|
| dm-integrity | ❌ DESHABILITADO |
| dm-verity | ❌ DESHABILITADO |
| Herramienta de integridad (AIDE, Tripwire…) | ❌ NO ENCONTRADA |
 
> **Riesgo significativo:** La ausencia de una herramienta de integridad de ficheros impide detectar modificaciones no autorizadas en archivos críticos del sistema. Se recomienda instalar y configurar **AIDE** (`apt install aide`) para monitorizar cambios en rutas sensibles como `/etc`, `/bin`, `/sbin` y `/usr`.
 
---
 
## 17. Hardening del Kernel (sysctl)
 
Comparativa entre los valores actuales y el perfil recomendado por Lynis:
 
### ✅ Parámetros correctamente configurados
 
| Parámetro sysctl | Valor esperado | Estado |
|---|---|---|
| `fs.protected_hardlinks` | 1 | ✅ OK |
| `fs.protected_regular` | 2 | ✅ OK |
| `fs.protected_symlinks` | 1 | ✅ OK |
| `kernel.ctrl-alt-del` | 0 | ✅ OK |
| `kernel.dmesg_restrict` | 1 | ✅ OK |
| `kernel.randomize_va_space` | 2 | ✅ OK |
| `kernel.yama.ptrace_scope` | 1/2/3 | ✅ OK |
| `net.ipv4.conf.all.accept_redirects` | 0 | ✅ OK |
| `net.ipv4.conf.all.accept_source_route` | 0 | ✅ OK |
| `net.ipv4.conf.all.log_martians` | 1 | ✅ OK |
| `net.ipv4.conf.all.send_redirects` | 0 | ✅ OK |
| `net.ipv4.conf.default.accept_redirects` | 0 | ✅ OK |
| `net.ipv4.icmp_echo_ignore_broadcasts` | 1 | ✅ OK |
| `net.ipv4.icmp_ignore_bogus_error_responses` | 1 | ✅ OK |
| `net.ipv4.tcp_syncookies` | 1 | ✅ OK |
| `net.ipv4.tcp_timestamps` | 0/1 | ✅ OK |
| `net.ipv6.conf.all.accept_source_route` | 0 | ✅ OK |
| `net.ipv6.conf.default.accept_source_route` | 0 | ✅ OK |
 
### ⚠️ Parámetros que difieren del perfil recomendado
 
| Parámetro sysctl | Valor esperado | Estado |
|---|---|---|
| `dev.tty.ldisc_autoload` | 0 | ❌ DIFFERENT |
| `fs.protected_fifos` | 2 | ❌ DIFFERENT |
| `fs.suid_dumpable` | 0 | ❌ DIFFERENT |
| `kernel.core_uses_pid` | 1 | ❌ DIFFERENT |
| `kernel.kptr_restrict` | 2 | ❌ DIFFERENT |
| `kernel.modules_disabled` | 1 | ❌ DIFFERENT |
| `kernel.perf_event_paranoid` | 3 | ❌ DIFFERENT |
| `kernel.sysrq` | 0 | ❌ DIFFERENT |
| `kernel.unprivileged_bpf_disabled` | 1 | ❌ DIFFERENT |
| `net.core.bpf_jit_harden` | 2 | ❌ DIFFERENT |
| `net.ipv4.conf.all.rp_filter` | 1 | ❌ DIFFERENT |
| `net.ipv4.conf.default.accept_source_route` | 0 | ❌ DIFFERENT |
| `net.ipv4.conf.default.log_martians` | 1 | ❌ DIFFERENT |
| `net.ipv6.conf.all.accept_redirects` | 0 | ❌ DIFFERENT |
| `net.ipv6.conf.default.accept_redirects` | 0 | ❌ DIFFERENT |
 
> Estos valores pueden ajustarse en `/etc/sysctl.conf` o en ficheros bajo `/etc/sysctl.d/`. Aplicar con `sudo sysctl -p` tras la modificación.
 
---
 
## 18. Permisos de Ficheros
 
### Ficheros con permisos correctos
| Fichero / Directorio | Estado |
|---|---|
| `/boot/grub/grub.cfg` | ✅ OK |
| `/etc/group` / `/etc/group-` | ✅ OK |
| `/etc/hosts.allow` / `/etc/hosts.deny` | ✅ OK |
| `/etc/issue` / `/etc/issue.net` | ✅ OK |
| `/etc/passwd` / `/etc/passwd-` | ✅ OK |
| `/root/.ssh` | ✅ OK |
 
### Ficheros con sugerencias de permisos
| Fichero / Directorio | Estado |
|---|---|
| `/etc/crontab` | ⚠️ SUGGESTION |
| `/etc/ssh/sshd_config` | ⚠️ SUGGESTION |
| `/etc/cron.d` | ⚠️ SUGGESTION |
| `/etc/cron.daily` | ⚠️ SUGGESTION |
| `/etc/cron.hourly` | ⚠️ SUGGESTION |
| `/etc/cron.weekly` | ⚠️ SUGGESTION |
| `/etc/cron.monthly` | ⚠️ SUGGESTION |
 
> Aplicar `chmod 600` o `chmod 700` según corresponda a los ficheros y directorios cron marcados como SUGGESTION.
 
---
 
## 19. Advertencias Detectadas
 
Se han detectado **2 advertencias** que requieren atención prioritaria:
 
### ⚠️ [MAIL-8818] – Divulgación de información en banner SMTP
 
El banner de Postfix expone el nombre del sistema operativo o software, lo que facilita la identificación del stack tecnológico por parte de un atacante.
 
**Solución:**
```bash
sudo postconf -e 'smtpd_banner = $myhostname ESMTP'
sudo systemctl reload postfix
```
 
Referencia: [https://cisofy.com/lynis/controls/MAIL-8818/](https://cisofy.com/lynis/controls/MAIL-8818/)
 
---
 
### ⚠️ [FIRE-4512] – Módulo iptables cargado sin reglas activas
 
El módulo de iptables está presente en el kernel pero no hay ninguna regla definida. El firewall efectivo opera únicamente a nivel de AWS Security Group.
 
**Solución recomendada:**
```bash
# Activar UFW como capa adicional de firewall local
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```
 
Referencia: [https://cisofy.com/lynis/controls/FIRE-4512/](https://cisofy.com/lynis/controls/FIRE-4512/)
 
---
 
## 20. Sugerencias de Mejora
 
A continuación se presentan las **41 sugerencias** emitidas por Lynis, agrupadas por categoría:
 
### Actualización y paquetes
 
| ID | Descripción |
|---|---|
| `LYNIS` | Versión de Lynis desactualizada (>4 meses). Actualizar desde GitHub |
| `DEB-0810` | Instalar `apt-listbugs` para alertas de bugs críticos previas a instalaciones |
| `DEB-0811` | Instalar `apt-listchanges` para seguimiento de cambios en upgrades |
| `PKGS-7370` | Instalar `debsums` para verificación de integridad de paquetes instalados |
| `PKGS-7394` | Instalar `apt-show-versions` para gestión de parches |
 
### Arranque y servicios
 
| ID | Descripción |
|---|---|
| `BOOT-5122` | Configurar contraseña en GRUB para proteger el modo de usuario único |
| `BOOT-5264` | Añadir sandboxing a servicios del sistema mediante directivas systemd |
 
### Kernel y sistema
 
| ID | Descripción |
|---|---|
| `KRNL-5820` | Deshabilitar core dumps en `/etc/security/limits.conf` |
| `KRNL-6000` | Ajustar valores sysctl que difieren del perfil de seguridad (ver sección 17) |
 
### Autenticación y contraseñas
 
| ID | Descripción |
|---|---|
| `AUTH-9262` | Instalar módulo PAM para verificación de fortaleza de contraseñas (`pam_cracklib` o `pam_passwdqc`) |
| `AUTH-9284` | Revisar y eliminar cuentas bloqueadas innecesarias |
| `AUTH-9328` | Establecer umask más restrictivo en `/etc/login.defs` (valor `027` recomendado) |
 
### Sistemas de ficheros
 
| ID | Descripción |
|---|---|
| `FILE-6310` | Mover `/home` a una partición separada para limitar el impacto de disco lleno |
| `FILE-6310` | Mover `/tmp` a una partición separada |
| `FILE-6310` | Mover `/var` a una partición separada |
| `FILE-7524` | Revisar y restringir permisos de ficheros (ver log de Lynis para detalle) |
 
### Red y protocolos
 
| ID | Descripción |
|---|---|
| `NETW-3200` | Evaluar y deshabilitar protocolo `dccp` si no es necesario |
| `NETW-3200` | Evaluar y deshabilitar protocolo `sctp` si no es necesario |
| `NETW-3200` | Evaluar y deshabilitar protocolo `rds` si no es necesario |
| `NETW-3200` | Evaluar y deshabilitar protocolo `tipc` si no es necesario |
| `NAME-4404` | Añadir IP y FQDN del host a `/etc/hosts` para resolución correcta |
| `USB-1000` | Deshabilitar el módulo `usb-storage` si no se utilizan dispositivos USB |
 
### Correo electrónico (Postfix)
 
| ID | Descripción |
|---|---|
| `MAIL-8818` | Ocultar `mail_name` en el banner de Postfix (ver advertencia en sección 19) |
| `MAIL-8820` | Deshabilitar el comando `VRFY` en Postfix: `postconf -e disable_vrfy_command=yes` |
 
### Servidor web (Nginx)
 
| ID | Descripción |
|---|---|
| `HTTP-6710` | Deshabilitar protocolos SSL/TLS débiles (TLSv1.0, TLSv1.1) |
| `HTTP-6710` | Configurar suite de ciphers explícita para mayor protección de datos en tránsito |
 
### SSH
 
| ID | Descripción |
|---|---|
| `SSH-7408` | Reducir `ClientAliveCountMax` de 3 a 2 |
| `SSH-7408` | Reducir `MaxSessions` de 10 a 2 |
| `SSH-7408` | Cambiar el puerto SSH del 22 a un puerto no estándar |
| `SSH-7408` | Establecer `TCPKeepAlive no` |
| `SSH-7408` | Establecer `AllowAgentForwarding no` |
 
### Logging y auditoría
 
| ID | Descripción |
|---|---|
| `LOGG-2154` | Habilitar logging a host externo para archivado y protección adicional |
| `LOGG-2190` | Investigar archivos eliminados que aún están en uso por procesos activos |
 
### Banners de advertencia legal
 
| ID | Descripción |
|---|---|
| `BANN-7126` | Añadir banner legal en `/etc/issue` para usuarios no autorizados |
| `BANN-7130` | Añadir banner legal en `/etc/issue.net` para conexiones remotas |
 
### Accounting y auditoría del sistema
 
| ID | Descripción |
|---|---|
| `ACCT-9622` | Habilitar process accounting |
| `ACCT-9626` | Habilitar `sysstat` para recopilación de métricas del sistema |
| `ACCT-9628` | Instalar y habilitar `auditd` para auditoría de llamadas al sistema |
 
### Integridad y herramientas
 
| ID | Descripción |
|---|---|
| `FINT-4350` | Instalar herramienta de integridad de ficheros (AIDE, Tripwire o similar) |
| `TOOL-5002` | Evaluar herramientas de automatización para gestión del sistema (Ansible, Puppet…) |
| `HRDN-7222` | Restringir acceso a compiladores instalados únicamente al usuario root |
 
---
 
## 21. Plan de Remediación Priorizado
 
A continuación se presenta un plan de acción ordenado por impacto y esfuerzo de implementación:
 
### 🔴 Prioridad Alta (implementar de inmediato)
 
| # | Acción | Control |
|---|---|---|
| 1 | Ocultar banner SMTP de Postfix | MAIL-8818 |
| 2 | Definir reglas de firewall local (UFW) | FIRE-4512 |
| 3 | Deshabilitar protocolos TLS inseguros en Nginx y definir ciphers | HTTP-6710 |
| 4 | Instalar y configurar AIDE para integridad de ficheros | FINT-4350 |
| 5 | Instalar y habilitar `auditd` | ACCT-9628 |
 
### 🟡 Prioridad Media (planificar en próximo sprint)
 
| # | Acción | Control |
|---|---|---|
| 6 | Ajustar parámetros sysctl pendientes (kernel hardening) | KRNL-6000 |
| 7 | Instalar módulo PAM de fortaleza de contraseñas | AUTH-9262 |
| 8 | Configurar logging remoto hacia SIEM Wazuh | LOGG-2154 |
| 9 | Deshabilitar comando VRFY en Postfix | MAIL-8820 |
| 10 | Añadir banners legales en `/etc/issue` e `/etc/issue.net` | BANN-7126/7130 |
| 11 | Ajustar opciones SSH pendientes (`ClientAliveCountMax`, `MaxSessions`, etc.) | SSH-7408 |
| 12 | Restringir acceso a compiladores | HRDN-7222 |
| 13 | Habilitar `sysstat` para métricas del sistema | ACCT-9626 |
 
### 🟢 Prioridad Baja (mejora continua)
 
| # | Acción | Control |
|---|---|---|
| 14 | Instalar `apt-listbugs` y `apt-listchanges` | DEB-0810/11 |
| 15 | Instalar `debsums` y `apt-show-versions` | PKGS-7370/7394 |
| 16 | Configurar contraseña en GRUB | BOOT-5122 |
| 17 | Añadir sandboxing systemd a servicios críticos | BOOT-5264 |
| 18 | Deshabilitar protocolos de red no utilizados (dccp, sctp, rds, tipc) | NETW-3200 |
| 19 | Mover `/home`, `/tmp`, `/var` a particiones separadas | FILE-6310 |
| 20 | Ajustar umask a `027` en `/etc/login.defs` | AUTH-9328 |
 
---
 
## Archivos de referencia
 
| Archivo | Ruta |
|---|---|
| Log completo de auditoría | `/var/log/lynis.log` |
| Reporte de datos | `/var/log/lynis-report.dat` |
| Perfil de configuración | `/etc/lynis/default.prf` |
| Directorio de plugins | `/etc/lynis/plugins` |
 
```bash
# Ver detalles de un control específico
lynis show details <TEST-ID>
 
# Consultar el log completo
less /var/log/lynis.log
 
# Subir resultados a Lynis Enterprise
lynis --upload
```
 
---
 
*Informe generado a partir de la auditoría de Lynis 3.0.9 ejecutada el 11 de mayo de 2026 sobre el host `ip-10-0-1-225` (Ubuntu 24.04 LTS). Para más información sobre los controles de seguridad, consultar [https://cisofy.com/lynis/](https://cisofy.com/lynis/).*
