# 🐳 Sprint 1 — Despliegue de Aplicaciones Vulnerables

En este sprint se instala Docker y se levantan las aplicaciones web vulnerables que actuarán como objetivos de práctica para el equipo atacante.

---

## 1. Instalación de Docker

```bash
sudo apt update
sudo apt install -y ca-certificates curl gnupg lsb-release
```

> **Captura — Instalación de dependencias:**
>
> ![Docker](../Imagenes%20Laboratotio/Docker.png)
---

## 2. Configuración de `docker-compose.yml`

Dentro del directorio `~/cyberarena-lab` se crea el fichero con los tres servicios vulnerables:

```yaml
version: '3.8'
services:

  # 1. DVWA (Damn Vulnerable Web App)
  dvwa:
    image: vulnerables/web-dvwa
    container_name: cyberarena_dvwa
    ports:
      - "8080:80"
    restart: unless-stopped

  # 2. OWASP Juice Shop
  juiceshop:
    image: bkimminich/juice-shop
    container_name: cyberarena_juiceshop
    ports:
      - "3000:3000"
    restart: unless-stopped

  # 3. bWAPP (Buggy Web Application)
  bwapp:
    image: hackersandslackers/bwapp
    container_name: cyberarena_bwapp
    ports:
      - "8081:80"
    restart: unless-stopped
```

```bash
docker-compose up -d
```

> **Captura — Editor nano con `docker-compose.yml` configurado:**
>
> ![Docker Compose](../Imagenes%20Laboratotio/Dockercompose.png)

---

## 3. Verificación de contenedores

```bash
sudo docker ps
```

Los contenedores `cyberarena_dvwa` y `cyberarena_juiceshop` aparecen como `Up`:

> **Captura — Salida de `docker ps`:**
>
> ![Docker PS](../Imagenes%20Laboratotio/Dockerps.png)

Verificación adicional via `curl`:

```bash
curl -I http://localhost:3000
```

Respuesta: `HTTP/1.1 200 OK`

> **Captura — Respuesta HTTP del servidor:**
>
> ![curl check](../Imagenes%20Laboratotio/Curl.png)

---

## 4. DVWA

| Parámetro | Valor |
|-----------|-------|
| URL | `http://localhost:8080` |
| Setup | `http://localhost:8080/setup.php` |
| Usuario | `admin` |
| Contraseña | `password` |

Acceder a `/setup.php` y pulsar **"Create / Reset Database"** para inicializar la base de datos.

> **Captura — Pantalla de Database Setup:**
>
> ![DVWA setup](../Imagenes%20Laboratotio/DVWA.png)

---

## 5. OWASP Juice Shop

| Parámetro | Valor |
|-----------|-------|
| URL | `http://localhost:3000` |

> **Captura — Juice Shop en el navegador:**
>
> ![Juice Shop](../Imagenes%20Laboratotio/JuiceShop.png)

---

## 6. Ejercicios realizados

### Command Injection (DVWA)

Payload inyectado en el campo "Ping a device":

```
127.0.0.1; cat /etc/passwd
```

Resultado: volcado completo del fichero `/etc/passwd` del contenedor.

> **Captura — Command Injection con lectura de `/etc/passwd`:**
>
> ![command injection](../Imagenes%20Laboratotio/payload.png)

---

### XSS Reflejado (DVWA)

Peticiones XSS confirmadas en los logs del contenedor:

```bash
sudo docker logs cyberarena_dvwa 2>&1 | grep "vulnerabilities/xss_d"
```

> ![command injection](../Imagenes%20Laboratotio/vulxss.png)

> **Captura — Logs con peticiones XSS y SQLi:**
>
> ![XSS logs](../Imagenes%20Laboratotio/vulbrut.png)

---

### SQL Injection blind (DVWA)

Payload detectado en los logs:

```
GET /vulnerabilities/sqli_blind/?id=1%27+UNION+SELECT+user%2C+password+FROM+users%23
```

---

### XSS en Juice Shop

Payload XSS inyectado en el buscador — servidor responde `403 Forbidden`:

> **Captura — 403 Forbidden ante payload XSS:**
>
> ![Juice Shop XSS](../Imagenes%20Laboratotio/xssforbiden.png)

---

### Retos completados en Juice Shop

```
✅ Solved 2-star loginAdminChallenge  (Login Admin)
✅ Solved 1-star scoreBoardChallenge  (Score Board)
```

---

## 7. Puertos del Sprint 1

| Servicio | Puerto host | Contenedor |
|----------|:-----------:|------------|
| DVWA | `8080` | `cyberarena_dvwa` |
| OWASP Juice Shop | `3000` | `cyberarena_juiceshop` |
| bWAPP | `8081` | `cyberarena_bwapp` |
