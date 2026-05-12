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
20. [Resultados de Lynis – Advertencias y Sugerencias](#20-resultados-de-lynis--advertencias-y-sugerencias)
21. [Medidas de Hardening Aplicadas](#21-medidas-de-hardening-aplicadas)
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
 
## 20. Resultados de Lynis – Advertencias y Sugerencias
 
Este es el output literal que Lynis muestra al final del escaneo, con los avisos que ha detectado.
 
### Warnings (2)
 
Lynis solo encontró 2 advertencias, que son los problemas más serios que hay que mirar primero.
 
```
! Found some information disclosure in SMTP banner (OS or software name) [MAIL-8818]
    https://cisofy.com/lynis/controls/MAIL-8818/
 
! iptables module(s) loaded, but no rules active [FIRE-4512]
    https://cisofy.com/lynis/controls/FIRE-4512/
```
 
**MAIL-8818** – El banner de Postfix está mostrando información del sistema operativo. Cualquiera que se conecte al puerto 25 puede ver qué software está corriendo, lo que facilita buscar vulnerabilidades específicas.
 
**FIRE-4512** – El módulo de iptables está cargado en el kernel pero no hay ninguna regla definida. El servidor depende únicamente del Security Group de AWS como firewall, sin ninguna capa de protección local.
 
---
 
### Suggestions (41)
 
Lynis generó 41 sugerencias. A continuación el output tal como aparece en la auditoría:
 
```
* This release is more than 4 months old. Check the website or GitHub to see
  if there is an update available. [LYNIS]
 
* Install apt-listbugs to display a list of critical bugs prior to each APT
  installation. [DEB-0810]
 
* Install apt-listchanges to display any significant changes prior to any
  upgrade via APT. [DEB-0811]
 
* Set a password on GRUB boot loader to prevent altering boot configuration
  (e.g. boot in single user mode without password) [BOOT-5122]
 
* Consider hardening system services [BOOT-5264]
  - Details: Run '/usr/bin/systemd-analyze security SERVICE' for each service
 
* If not required, consider explicit disabling of core dump in
  /etc/security/limits.conf file [KRNL-5820]
 
* Install a PAM module for password strength testing like pam_cracklib or
  pam_passwdqc [AUTH-9262]
 
* Look at the locked accounts and consider removing them [AUTH-9284]
 
* Default umask in /etc/login.defs could be more strict like 027 [AUTH-9328]
 
* To decrease the impact of a full /home file system, place /home on a
  separate partition [FILE-6310]
 
* To decrease the impact of a full /tmp file system, place /tmp on a
  separate partition [FILE-6310]
 
* To decrease the impact of a full /var file system, place /var on a
  separate partition [FILE-6310]
 
* Disable drivers like USB storage when not used, to prevent unauthorized
  storage or data theft [USB-1000]
 
* Add the IP name and FQDN to /etc/hosts for proper name resolving [NAME-4404]
 
* Install debsums utility for the verification of packages with known good
  database. [PKGS-7370]
 
* Install package apt-show-versions for patch management purposes [PKGS-7394]
 
* Determine if protocol 'dccp' is really needed on this system [NETW-3200]
* Determine if protocol 'sctp' is really needed on this system [NETW-3200]
* Determine if protocol 'rds' is really needed on this system [NETW-3200]
* Determine if protocol 'tipc' is really needed on this system [NETW-3200]
 
* You are advised to hide the mail_name (option: smtpd_banner) from your
  postfix configuration. Use postconf -e or change your main.cf file
  (/etc/postfix/main.cf) [MAIL-8818]
 
* Disable the 'VRFY' command [MAIL-8820]
  - Details  : disable_vrfy_command=no
  - Solution : run postconf -e disable_vrfy_command=yes to change the value
 
* Disable weak protocol in nginx configuration [HTTP-6710]
 
* Change the HTTPS and SSL settings for enhanced protection of sensitive data
  and privacy [HTTP-6710]
 
* Consider hardening SSH configuration [SSH-7408]
  - Details: ClientAliveCountMax (set 3 to 2)
 
* Consider hardening SSH configuration [SSH-7408]
  - Details: MaxSessions (set 10 to 2)
 
* Consider hardening SSH configuration [SSH-7408]
  - Details: Port (set 22 to <otro puerto>)
 
* Consider hardening SSH configuration [SSH-7408]
  - Details: TCPKeepAlive (set YES to NO)
 
* Consider hardening SSH configuration [SSH-7408]
  - Details: AllowAgentForwarding (set YES to NO)
 
* Enable logging to an external logging host for archiving purposes and
  additional protection [LOGG-2154]
 
* Check what deleted files are still in use and why. [LOGG-2190]
 
* Add a legal banner to /etc/issue, to warn unauthorized users [BANN-7126]
 
* Add legal banner to /etc/issue.net, to warn unauthorized users [BANN-7130]
 
* Enable process accounting [ACCT-9622]
 
* Enable sysstat to collect accounting (disabled) [ACCT-9626]
 
* Enable auditd to collect audit information [ACCT-9628]
 
* Install a file integrity tool to monitor changes to critical and sensitive
  files [FINT-4350]
 
* Determine if automation tools are present for system management [TOOL-5002]
 
* Consider restricting file permissions [FILE-7524]
  - Details  : See screen output or log file
  - Solution : Use chmod to change file permissions
 
* One or more sysctl values differ from the scan profile and could be tweaked
  [KRNL-6000]
  - Solution : Change sysctl value or disable test
    (skip-test=KRNL-6000:<sysctl-key>)
 
* Harden compilers like restricting access to root user only [HRDN-7222]
```
 
Las sugerencias más relevantes a destacar son las de SSH, ya que indican parámetros concretos con el valor actual y el valor al que habría que cambiarlo. Las de `FILE-6310` sobre particiones separadas para `/home`, `/tmp` y `/var` son habituales en entornos de producción pero en este caso no aplica porque la instancia es una EC2 con un único volumen. Las sugerencias de `NETW-3200` sobre protocolos como `dccp`, `sctp`, `rds` o `tipc` se resuelven añadiéndolos a la lista negra de módulos del kernel en `/etc/modprobe.d/`.
 
---
 
## 21. Medidas de Hardening Aplicadas
 
Tras el primer escaneo de Lynis (índice 66), se aplicaron una serie de medidas de seguridad que subieron el índice hasta **73**. A continuación se detalla lo que se hizo en cada área, relacionándolo con lo que Lynis había señalado.
 
---
 
### WAF – ModSecurity + OWASP CRS
 
Se instaló ModSecurity como WAF integrado en Nginx. Por defecto viene en modo `DetectionOnly`, así que lo primero fue cambiar eso:
 
```bash
sudo mv /etc/modsecurity/modsecurity.conf-recommended /etc/modsecurity/modsecurity.conf
sudo sed -i 's/SecRuleEngine DetectionOnly/SecRuleEngine On/' /etc/modsecurity/modsecurity.conf
```
 
Se reinstalaron las reglas del OWASP Core Rule Set (versión `3.3.5-2`) para tener una base limpia, quedando **915 reglas** activas. Se verificó el bloqueo real lanzando un ataque SQLi desde otra máquina y comprobando que devolvía `403 Forbidden`.
 
---
 
### SSH – Hardening de `sshd_config`
 
Lynis marcaba varias opciones SSH como `SUGGESTION`. Se aplicaron los siguientes cambios en `/etc/ssh/sshd_config`:
 
```bash
MaxAuthTries 3          # Limita intentos de autenticación por sesión
AllowTcpForwarding no   # Evita usar el servidor como proxy SSH
X11Forwarding no        # Deshabilita reenvío de aplicaciones gráficas
SyslogFacility AUTH     # Logging de autenticación
LogLevel VERBOSE        # Registra detalles de cada conexión, incluyendo claves usadas
```
 
Con esto, las opciones `AllowTcpForwarding`, `LogLevel`, `MaxAuthTries` y `X11Forwarding` pasaron a estar en `OK` en el segundo escaneo.
 
---
 
### Contraseñas – Política de caducidad y hash
 
Se reforzó la política de contraseñas en `/etc/login.defs`:
 
```bash
PASS_MAX_DAYS   90      # La contraseña caduca a los 90 días
PASS_MIN_DAYS   7       # No se puede cambiar antes de 7 días
PASS_WARN_AGE   7       # Aviso 7 días antes de la caducidad
 
SHA_CRYPT_MIN_ROUNDS 10000   # Rondas mínimas de hash
SHA_CRYPT_MAX_ROUNDS 10000   # Rondas máximas de hash
```
 
Más rondas de hash significa que un ataque de fuerza bruta offline es mucho más lento. Lynis confirmó estas opciones como `CONFIGURED` en el segundo escaneo.
 
---
 
### Red – Parámetros del kernel (`sysctl`)
 
Se añadieron parámetros en `/etc/sysctl.conf` para endurecer la pila de red:
 
```bash
net.ipv4.conf.all.accept_redirects = 0       # No aceptar redirecciones ICMP
net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.all.send_redirects = 0         # No enviar redirecciones (no somos router)
net.ipv4.conf.all.log_martians = 1           # Registrar paquetes con IPs imposibles
```
 
Estos parámetros corresponden a los controles `KRNL-6000` de Lynis. En el segundo escaneo, los cuatro aparecen como `OK`.
 
---
 
### Fail2Ban – Protección SSH contra fuerza bruta
 
Se configuró Fail2Ban con una jail específica para SSH:
 
```ini
[sshd]
enabled  = true
port     = ssh
filter   = sshd
logpath  = /var/log/auth.log
maxretry = 3
bantime  = 1h
```
 
Tras 3 intentos fallidos de login, la IP queda baneada 1 hora automáticamente. Lynis detectó Fail2Ban instalado con `jail.local` activo.
 
---
 
### MariaDB – Securización post-instalación
 
Se ejecutó `mysql_secure_installation` eliminando la configuración insegura por defecto:
 
| Acción | Resultado |
|---|---|
| Eliminar usuarios anónimos | ✅ |
| Deshabilitar login root remoto | ✅ |
| Eliminar base de datos `test` | ✅ |
| Unix socket authentication | ✅ Habilitado |
 
---
 
### HIDS – Agente Wazuh
 
Se instaló y configuró el agente Wazuh conectado al servidor central en `10.0.1.96:1514/TCP`. Esto cubre parcialmente la sugerencia `LOGG-2154` de Lynis sobre envío de logs a un host externo, ya que Wazuh recoge eventos del sistema en tiempo real.
 
---
 
### Backups – Script automatizado con cron
 
Se creó el script `/usr/local/bin/backup_cyberarena.sh` que realiza:
- Backup de la base de datos con `mysqldump` → `db_backup_FECHA.sql`
- Backup del directorio web `/var/www/html` → `web_backup_FECHA.tar.gz`
- Limpieza automática de backups con más de 7 días
Se programó en crontab para ejecutarse cada día a las 03:00 AM:
 
```bash
00 03 * * * /usr/local/bin/backup_cyberarena.sh >> /var/log/backup_cyberarena.log 2>&1
```
 
---
 
### Resultado comparativo Lynis
 
| Métrica | Antes del hardening | Después del hardening |
|---|---|---|
| Hardening Index | **66** | **73** |
| Tests realizados | 266 | 267 |
| Plugins habilitados | 0 | 1 |
 
La mejora de **7 puntos** se debe directamente a las configuraciones aplicadas en SSH, contraseñas, kernel y la detección de Fail2Ban y el malware scanner (chkrootkit).
 
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


## Resumen
 
La auditoría con Lynis 3.0.9 sobre el servidor `ip-10-0-1-225` (Ubuntu 24.04 LTS) arrojó un **Hardening Index final de 73/100** tras aplicar las medidas de hardening, partiendo de un índice inicial de 66.
 
Las mejoras más significativas vinieron de endurecer la configuración SSH (`MaxAuthTries`, `LogLevel VERBOSE`, sin reenvío TCP ni X11), aplicar política de contraseñas con caducidad y rondas de hash elevadas, añadir parámetros de red al kernel para prevenir ataques MITM, e instalar ModSecurity como WAF con el OWASP CRS activo en modo bloqueo.
 
Quedan pendientes de abordar las 2 advertencias activas (banner legal y fortaleza de contraseñas PAM) y las sugerencias más relevantes: habilitar `auditd`, configurar logging externo, deshabilitar protocolos de red innecesarios (`dccp`, `sctp`, `rds`, `tipc`) y añadir sandboxing a los servicios críticos como `nginx`, `mariadb` y `ssh` mediante directivas de systemd.
 
Es importante tener en cuenta que el hardening tiene un límite práctico: cuanto más se restringe el sistema, más difícil se vuelve no solo entrar para un atacante, sino también para los propios administradores y los servicios que corren en el servidor. Aplicar todas las sugerencias de Lynis sin criterio puede romper funcionalidades, dejar servicios inaccesibles o hacer inoperable el servidor. Cada medida debe evaluarse en el contexto del entorno, priorizando las que aportan más seguridad con menos impacto operativo.
