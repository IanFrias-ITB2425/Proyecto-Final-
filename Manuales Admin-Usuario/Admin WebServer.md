# 🔧 Manual de Administrador: Web Server CyberArena

Este manual está destinado al responsable del Web Server para el mantenimiento, diagnóstico y recuperación del servidor.

---

## 1. Datos de la Instancia

| Parámetro | Valor |
|---|---|
| Proveedor | AWS EC2 |
| Tipo | `t3.micro` |
| Sistema Operativo | Ubuntu 24.04 LTS |
| Dominio | `cyberarena-admin.duckdns.org` |
| Raíz web | `/var/www/html` |
| Base de datos | `arena_db` (MariaDB) |
| Usuario BD aplicación | `arena_sys@localhost` |

---

## 2. Conexión al Servidor

```bash
# Asignar permisos a la clave si es la primera vez
chmod 400 clave-proyecto.pem

# Conectar
ssh -i "clave-proyecto.pem" ubuntu@<IP_PUBLICA>
```

La IP pública puede consultarse en la consola de AWS EC2. Si la instancia se ha reiniciado y la IP ha cambiado, hay que actualizar el registro A de DuckDNS (ver sección 6).

---

## 3. Gestión de Servicios

Comandos habituales para gestionar el stack:

```bash
# Nginx
sudo systemctl status nginx
sudo systemctl reload nginx     # Recargar config sin cortar conexiones
sudo systemctl restart nginx    # Reinicio completo

# MariaDB
sudo systemctl status mariadb
sudo systemctl restart mariadb

# PHP-FPM
sudo systemctl status php*-fpm
sudo systemctl restart php*-fpm

# Wazuh Agent
sudo systemctl status wazuh-agent
sudo systemctl restart wazuh-agent
```

Antes de reiniciar Nginx, siempre validar la configuración:

```bash
sudo nginx -t
```

---

## 4. Verificación del WAF (ModSecurity)

Comprobar que ModSecurity está cargado y activo:

```bash
sudo nginx -t
# Debe mostrar: ModSecurity-nginx v1.0.3 (rules loaded ... 915 ...)
```

Revisar logs de bloqueos:

```bash
sudo tail -f /var/log/nginx/error.log | grep ModSecurity
```

Si hay que deshabilitar ModSecurity temporalmente para diagnosticar un problema, editar el virtual host y cambiar `modsecurity on` por `modsecurity off`, luego recargar Nginx. Volver a activarlo en cuanto se resuelva el problema.

---

## 5. Gestión de Backups

Los backups se ejecutan automáticamente cada día a las 03:00 AM y se almacenan en `/root/backups`.

```bash
# Ver backups existentes
sudo ls -lh /root/backups

# Ejecutar backup manualmente
sudo /usr/local/bin/backup_cyberarena.sh

# Revisar log del último backup
sudo tail -50 /var/log/backup_cyberarena.log
```

Los archivos se borran automáticamente tras 7 días. Si el disco se llena antes, revisar si el script de limpieza está funcionando correctamente.

---

## 6. Actualización del Dominio DuckDNS

Si la IP pública de la instancia cambia (por reinicio del laboratorio AWS):

1. Consultar la nueva IP en la consola de AWS EC2.
2. Entrar en [https://www.duckdns.org](https://www.duckdns.org) con la cuenta del proyecto.
3. Actualizar el registro `cyberarena-admin` con la nueva IP.
4. Verificar que el dominio resuelve correctamente:

```bash
ping cyberarena-admin.duckdns.org
```

---

## 7. Renovación del Certificado SSL

El certificado de Let's Encrypt se renueva automáticamente. Para forzar la renovación manualmente:

```bash
sudo certbot renew --nginx
sudo systemctl reload nginx
```

Para comprobar la fecha de expiración del certificado actual:

```bash
sudo certbot certificates
```

---

## 8. Diagnóstico de la Base de Datos

```bash
# Acceder a MariaDB como root
sudo mysql -u root

# Verificar que la base de datos existe
SHOW DATABASES;

# Verificar tablas de arena_db
USE arena_db;
SHOW TABLES;

# Comprobar el usuario de la aplicación
SELECT user, host FROM mysql.user WHERE user = 'arena_sys';
```

Si el dashboard muestra `CRITICAL` en la base de datos, comprobar que el servicio MariaDB está activo y que el usuario `arena_sys` puede conectarse:

```bash
mysql -u arena_sys -p arena_db
```

---

## 9. Restauración del Servidor desde Cero

Si hay que rehacer la instancia por completo (por ejemplo, por expiración de la suscripción AWS), seguir en orden los documentos de documentación técnica:

1. **WebServer.md** — Despliegue del stack (Nginx, MariaDB, PHP, SSL, dominio)
2. **WebServer2.md** — Hardening y seguridad (SSH, WAF, Wazuh, backups, Lynis)

Ambos documentos tienen todos los comandos necesarios para replicar el entorno completo.

---

## 10. Comprobaciones de Seguridad Periódicas

```bash
# Ver IPs baneadas por Fail2Ban
sudo fail2ban-client status sshd

# Desbanear una IP manualmente
sudo fail2ban-client set sshd unbanip <IP>

# Ver últimas entradas del log de autenticación SSH
sudo tail -50 /var/log/auth.log

# Ejecutar auditoría de seguridad Lynis
sudo lynis audit system
```

El índice de seguridad de referencia es **73** (resultado obtenido tras el hardening del Sprint 2). Si en una auditoría futura el índice baja significativamente, revisar si alguna configuración ha sido modificada.
