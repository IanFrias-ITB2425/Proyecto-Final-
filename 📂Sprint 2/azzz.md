# **Documentación Técnica – Implementación, Despliegue y Configuración Completa del SOC Central**

**Proyecto:** CyberArena – Plataforma de Ciberseguridad Centralizada  
**Proveedor Cloud:** Amazon Web Services (AWS)  
**Sistema Operativo Base:** Amazon Linux 2023 (Kernel 6.1) / Nodos Cliente en Ubuntu 24.04 LTS  
**Fecha de despliegue:** Mayo 2026  

---

## 📋 Índice

* [1. Instalación de la Arquitectura Central de Wazuh SIEM](#1-instalación-de-la-arquitectura-central-de-wazuh-siem)
* [2. Despliegue de la Red Privada Virtual Cifrada (Wireguard VPN)](#2-despliegue-de-la-red-privada-virtual-cifrada-wireguard-vpn)
* [3. Instalación y Alta de Agentes de Seguridad Distribuidos](#3-instalación-y-alta-de-agentes-de-seguridad-distribuidos)
  * [3.1 Servidor Web](#31-servidor-web)
  * [3.2 Servidor Honeypot](#32-servidor-honeypot)
* [4. Extracción Activa de Logs de Contenedores Docker](#4-extracción-activa-de-logs-de-contenedores-docker)
* [5. Configuración del Motor de Integridad de Archivos en Tiempo Real](#5-configuración-del-motor-de-integridad-de-archivos-en-tiempo-real)
* [6. Integración de API de Webhooks de Discord](#6-integración-de-api-de-webhooks-de-discord)
* [7. Conexión con la API de Inteligencia Antivirus de VirusTotal](#7-conexión-con-la-api-de-inteligencia-antivirus-de-virustotal)
  * [7.1 Registro en VirusTotal](#71-registro-en-virustotal)
  * [7.2 Implementación en Wazuh](#72-implementación-en-wazuh)
  * [7.3 Comprobación](#73-comprobación)
* [8. Configuración de Dominio Dinámico Gratuito (Duck DNS)](#8-configuración-de-dominio-dinámico-gratuito-duck-dns)
  * [8.1 Registro en Duck DNS](#81-registro-en-duck-dns)
  * [8.2 Instalación de Certbot y Obtención del Certificado SSL](#82-instalación-de-certbot-y-obtención-del-certificado-ssl)
* [9. Vinculación del Certificado SSL en Wazuh Dashboard](#9-vinculación-del-certificado-ssl-en-wazuh-dashboard)
* [10. Errores y Soluciones](#10-errores-y-soluciones)
  * [10.1 Fallo de Validación del Contexto SSL en el Entorno Virtual Aislado de Python](#101-fallo-de-validación-del-contexto-ssl-en-el-entorno-virtual-aislado-de-python)
  * [10.2 Restricción de Permisos UNIX](#102-restricción-de-permisos-unix)
  * [10.3 Degradación de Petición por Redirección HTTP con Pérdida de Payload](#103-degradación-de-petición-por-redirección-http-con-pérdida-de-payload)
  * [10.4 Rechazo de Payload por Incompatibilidad de Formato en el Timestamp](#104-rechazo-de-payload-por-incompatibilidad-de-formato-en-el-timestamp)

---

## 1. Instalación de la Arquitectura Central de Wazuh SIEM

Una vez dentro de la terminal de AWS, procedimos con la descarga e instalación del motor centralizado de Wazuh.

_Descargar y ejecutar el instalador automatizado:_  
```bash
 curl -sO [https://packages.wazuh.com/4.14/wazuh-install.sh](https://packages.wazuh.com/4.14/wazuh-install.sh) && sudo bash ./wazuh-install.sh -a
```

Una vez finalizada la descarga y descompresión de los archivos, el instalador nos proporcionó las credenciales de acceso del perfil administrativo, las cuales utilizaríamos para acceder a Wazuh y poder gestionarlo:

* **Usuario maestro:** admin  
* **Contraseña generada:** M7xRS1KfhjYHlu?PSpB45NaT9UQ490Sb  

A partir de este momento, comprobamos que el clúster respondía tecleando la dirección IP asignada directamente en la barra del navegador: [https://3.229.242.100/](https://3.229.242.100/).

![[📸 Captura: Acceso Inicial Interfaz Web por IP]](img-SOC-Wazuh/3.png)

---

## 2. Despliegue de la Red Privada Virtual Cifrada (Wireguard VPN)

Como medida de confidencialidad para que los logs de auditoría interna de nuestras máquinas no viajen expuestos a internet, configuramos una red privada virtual (VPN) ligera y de alta velocidad basada en el protocolo **Wireguard**.

_Instalar el paquete de utilidades de Wireguard en el sistema:_  
```bash
sudo dnf install wireguard-tools -y
```

![[📸 Captura: Instalación de Paquetes Wireguard]](img-SOC-Wazuh/4.png)

Ahora es necesario generar las credenciales criptográficas de comunicación:

_Generar la pareja de claves criptográficas asimétricas en el servidor maestro:_  
```bash
wg genkey | tee privatekeywazuh | wg pubkey > publickeywazuh
```

Creamos el archivo de control de la interfaz virtual `/etc/wireguard/wg0.conf` asignando las siguientes directivas de enrutamiento seguro:

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

![[📸 Captura: Fichero de Configuración wg0.conf]](img-SOC-Wazuh/5.png)

**Solución a error de enrutamiento:** Al levantar la interfaz virtual se detectó el fallo crítico `iptables-restore: command not found` debido a que Amazon Linux 2023 carece de herramientas de red obsoletas de manera nativa. 

_Corregir la incidencia mediante la instalación del subsistema de traducción NFTables moderno:_  
```bash
sudo dnf install iptables-nft -y
```

---

## 3. Instalación y Alta de Agentes de Seguridad Distribuidos

Con el servidor maestro escuchando peticiones de red, entramos en las consolas de nuestros dos servidores (`server-web` y `honeypots`) para hacer que el agente local de Wazuh pueda recolectar los eventos internos de cada máquina.

Para ello hay que entrar en Wazuh. Dentro de él accederemos al desplegable lateral y entraremos dentro de Server Manager, en él aparecerá un desplegable y tendremos que entrar en la opción Endpoints Summary.

![[📸 Captura: Navegación hacia Endpoints Summary]](img-SOC-Wazuh/6.png)

Una vez dentro, pulsamos el botón **Deploy new agent**.

![[📸 Captura: Botón Deploy New Agent]](img-SOC-Wazuh/7.png)

### 3.1 Servidor Web

La integración de Wazuh en cada máquina es personalizada y única, aunque el proceso inicial es similar.

Para el servidor web, como sistema operativo de destino seleccionamos un entorno Linux con arquitectura **DEB amd64**, debido a que el servidor web está montado sobre una distribución Ubuntu Desktop.

![[📸 Captura: Selección Sistema Operativo Agente]](img-SOC-Wazuh/8.png)

En el campo *Server Address* introducimos la dirección IP del servidor centralizado encargado de recolectar todos los registros (`3.229.242.100`).

![[📸 Captura: Configuración IP del Manager]](img-SOC-Wazuh/9.png)

En la sección **Optional Settings** asignamos un nombre descriptivo al agente. Tratándose del servidor web, optamos por el identificador **Servidor-Web**. Es necesario contemplar que a partir de este momento ningún otro agente del entorno podrá utilizar el mismo nombre.

![[📸 Captura: Asignación de Nombre del Agente Web]](img-SOC-Wazuh/10.png)

A continuación, accedemos a la máquina que deseamos monitorizar (el servidor web) y ejecutamos el comando personalizado generado de forma automática por el panel de control de Wazuh:

_Descargar el paquete deb oficial e instalar el agente asociándolo al Manager:_  
```bash
wget [https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent_4.9.2-1_amd64.deb](https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent_4.9.2-1_amd64.deb) && sudo WAZUH_MANAGER='3.229.242.100' WAZUH_AGENT_NAME='Servidor-Web' dpkg -i ./wazuh-agent_4.9.2-1_amd64.deb
```

_Recargar los demonios del sistema, habilitar el inicio automático y arrancar el agente local:_  
```bash
sudo systemctl daemon-reload
sudo systemctl enable wazuh-agent
sudo systemctl start wazuh-agent
```

Completados estos pasos, en la consola central de Wazuh aparecerá listado y activo el nuevo Endpoint con el nombre asignado.

![[📸 Captura: Agente Servidor-Web Conectado]](img-SOC-Wazuh/11.png)

### 3.2 Servidor Honeypot

En este servidor el procedimiento es idéntico, adaptando los parámetros específicos para este nodo dentro del asistente de despliegue.

* **Selección de S.O:** Linux DEB amd64
* **Server Address:** `3.229.242.100`

Como nombre identificativo para esta máquina virtual optamos por llamarla **Honeypots**, reflejando su función de albergar servicios controlados destinados a confundir y registrar los movimientos de atacantes externos.

![[📸 Captura: Asignación de Nombre Agente Honeypot]](img-SOC-Wazuh/12.png)

Una vez introducidos los datos específicos, ejecutamos el comando resultante dentro de la consola del servidor dedicado a los Honeypots:

_Descargar el paquete deb oficial e instalar el agente asociándolo al nodo de monitorización:_  
```bash
wget [https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent_4.9.2-1_amd64.deb](https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent_4.9.2-1_amd64.deb) && sudo WAZUH_MANAGER='3.229.242.100' WAZUH_AGENT_NAME='Honeypots' dpkg -i ./wazuh-agent_4.9.2-1_amd64.deb
```

_Activar el arranque del servicio y sincronizar los logs en el sistema operativo:_  
```bash
sudo systemctl daemon-reload
sudo systemctl enable wazuh-agent
sudo systemctl start wazuh-agent
```

Una vez ejecutadas las instrucciones dentro de la máquina cliente, comprobamos en el panel de control central que el nuevo nodo ha quedado vinculado de forma exitosa.

![[📸 Captura: Vista General de Agente Vinculado 1]](img-SOC-Wazuh/13.png)  
![[📸 Captura: Vista General de Agente Vinculado 2]](img-SOC-Wazuh/14.png)

---

## 4. Extracción Activa de Logs de Contenedores Docker

Para dotar al nodo **Servidor Honeypots** de una superficie de ataque realista y atractiva para los atacantes de internet, desplegamos mediante contenedores Docker aislados las aplicaciones vulnerables de referencia en auditorías: **OWASP Juice Shop** y **DVWA**. Como Docker encapsula sus registros de forma nativa bloqueando el acceso a agentes externos, programamos una redirección de flujo log en segundo plano.

_Redirigir las salidas estándar de logs de los contenedores hacia ficheros planos persistentes:_  
```bash
sudo sh -c 'nohup docker logs -f cyberarena_juiceshop > /var/log/juiceshop.log 2>&1 &'
sudo sh -c 'nohup docker logs -f cyberarena_dvwa > /var/log/dvwa.log 2>&1 &'
```

Editamos el archivo de configuración local del agente `/var/ossec/etc/ossec.conf` dentro del propio servidor Honeypots y añadimos los siguientes bloques al final del documento para canalizar la lectura de dichos logs hacia nuestro Manager de AWS:

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

![[📸 Captura: Bloques localfile en ossec.conf del Honeypot]](img-SOC-Wazuh/15.png)

---

## 5. Configuración del Motor de Integridad de Archivos en Tiempo Real

Para vigilar con precisión absoluta e instantánea la aparición de troyanos, *webshells* de persistencia o alteraciones ilegítimas en nuestros directorios de trabajo, modificamos manualmente las directivas del motor **Syscheck FIM** en ambas máquinas cliente.

### 5.1 Líneas integradas dentro del archivo ossec.conf local del Servidor-Web:

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

### 5.2 Líneas integradas dentro del archivo ossec.conf local del Servidor Honeypots:

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

Al activar la propiedad `realtime="yes"`, los agentes remotos se enlazan directamente con las llamadas de interrupción del subsistema del kernel de Linux mediante la API `inotify`. Esto provoca el envío inmediato del hash SHA256 al servidor centralizado en el mismo milisegundo en el que un archivo toca el almacenamiento físico, aunando por completo la ventana de exposición latente de 12 horas establecida por defecto en la plataforma.

_Aplicar y consolidar las políticas modificadas reiniciando los agentes remotos:_  
```bash
sudo systemctl restart wazuh-agent
```

---

## 6. Integración de API de Webhooks de Discord

Para conseguir visibilidad total e instantánea ante cualquier vector de ataque detectado, configuramos una pasarela automatizada para desviar cualquier incidente crítico de **Nivel de riesgo igual o superior a 7** hacia nuestro canal corporativo de operaciones de Discord.

### Pasos de Configuración en el Servidor Central (AWS):

1. Creamos el canal de texto `#alertas-wazuh` en nuestro servidor privado de Discord y generamos un Webhook de integración.

![[📸 Captura: Creación de Integración de Discord 1]](img-SOC-Wazuh/16.png)  
![[📸 Captura: Creación de Integración de Discord 2]](img-SOC-Wazuh/17.png)

Definimos un nombre identificativo para la integración, en este caso configurado como **SOC - Wazuh Alerts**.

![[📸 Captura: Nombre asignado al Bot]](img-SOC-Wazuh/16.png)

Añadimos el nuevo canal dedicado bajo el nombre `alertas-wazuh`. Para ello, pulsamos el botón **+** ubicado en el bloque de canales de texto.

Establecemos la tipología de canal como **Texto** e introducimos el nombre seleccionado. En escenarios corporativos reales, resulta idóneo activar la casilla de **Canal Privado**, asegurando que únicamente los analistas de seguridad explíitamente autorizados por el administrador puedan visualizar los reportes de incidentes, aislando los datos críticos del resto de departamentos.

![[📸 Captura: Panel de Creación de Canal de Texto]](img-SOC-Wazuh/19.png)

Consolidado el canal, accedemos a su configuración interna dentro del apartado de **Integraciones** para dar de alta y extraer las credenciales del Webhook encargado de enlazar el SIEM externo.

![[📸 Captura: Panel de Configuración de Integración de Canal 1]](img-SOC-Wazuh/20.png)  
![[📸 Captura: Panel de Configuración de Integración de Canal 2]](img-SOC-Wazuh/21.png)

Hacemos clic en la opción *Nuevo Webhook*, lo bautizamos como **Wazuh Bot**, copiamos la URL única asignada y guardamos las modificaciones.

![[📸 Captura: Generación e Ingesta de URL Webhook]](img-SOC-Wazuh/22.png)

2. **El bypass de compatibilidad:** Debido a que Wazuh cuenta de forma nativa con integración madura para los payloads de Slack pero carece de conector directo para Discord, aprovechamos la pasarela de compatibilidad de la API de Discord **añadiendo el sufijo `/slack` al final de la URL** generada en el paso anterior.

3. Abrimos el archivo `/var/ossec/etc/ossec.conf` de nuestro **Wazuh Manager** en AWS e inyectamos el bloque definitivo de alerta automatizada:

```xml
<integration>
  <name>slack</name>
  <level>7</level>
  <hook_url>[https://discord.com/api/webhooks/1502728258163314859/Sf4Qx2rJbq3NjMX0e3l1CmobEBiOHf_Yoe5EMVPngo8Ngu0aPHyAm1KyXh8OupP0VB3z/slack](https://discord.com/api/webhooks/1502728258163314859/Sf4Qx2rJbq3NjMX0e3l1CmobEBiOHf_Yoe5EMVPngo8Ngu0aPHyAm1KyXh8OupP0VB3z/slack)</hook_url>
  <alert_format>json</alert_format>
</integration>
```

![[📸 Captura: Bloque de Integración en ossec.conf del Manager]](img-SOC-Wazuh/23.png)

---

## 7. Conexión con la API de Inteligencia Antivirus de VirusTotal

La segunda API Key que integramos es la de VirusTotal. Gracias a esta API Key si alguien intenta introducir o un virus o ha descargado un virus dentro de los servicios y es algún virus conocido por la base de datos de VirusTotal lo detectará y nos enviará un aviso tanto a Wazuh como al Discord.

Si se diera la situación de que un ciberdelincuente empleara un exploit inédito desarrollado a medida (*Zero-Day*), VirusTotal carecería de registros, pero los desencadenantes de comportamiento locales de Wazuh registrarían igualmente la intrusión. Al contar Wazuh con integración nativa directa con este servicio de inteligencia, el despliegue es altamente eficiente.

### 7.1 Registro en VirusTotal

En primer lugar, accedemos a la plataforma oficial de [virustotal.com](http://virustotal.com) y procedemos al alta de un perfil de auditoría para extraer la clave de comunicación (API Key).

![[📸 Captura: Obtención de API Key en Perfil]](img-SOC-Wazuh/24.png)

Dentro del panel de control de la API Key podemos realizar un seguimiento preciso de las cuotas de peticiones permitidas, análisis activos y métricas de consumo diario de la clave de confianza.

![[📸 Captura: Estadísticas y Cuota de API Key]](img-SOC-Wazuh/25.png)

### 7.2 Implementación en Wazuh

Con la clave copiaba, accedemos al archivo principal del Manager en AWS ejecutando `sudo nano /var/ossec/etc/ossec.conf` e integramos al final del documento el siguiente módulo vinculando nuestra API Key:

```xml
<virustotal>
  <enabled>yes</enabled>
  <api_key>d7512cfed73a44b500d86ed47b820092877cc86ff91874f0cc005a4b66b5325c</api_key>
  <group>syscheck</group>
  <alert_format>json</alert_format>
</virustotal>
```

![[📸 Captura: Bloque virustotal inyectado en el Manager]](img-SOC-Wazuh/26.png)

_Reiniciar el servicio maestro para habilitar el nuevo motor de escaneo:_  
```bash
sudo systemctl restart wazuh-manager
```

Establecido el cerebro, nos trasladamos a las máquinas remotas cliente que deseamos vigilar (`Servidor-Web` y `Honeypots`) para ajustar el comportamiento de las alertas. Editamos su fichero local `/var/ossec/etc/ossec.conf` en la etiqueta de integridad `syscheck`. Por defecto, el bloque viene configurado de la siguiente manera:

```xml
<syscheck>
  <disabled>no</disabled>
  <frequency>43200</frequency>
  <scan_on_start>yes</scan_on_start>
  <directories>/etc,/usr/bin,/usr/sbin</directories>
  <directories>/bin,/sbin,/boot</directories>
</syscheck>
```

![[📸 Captura: Estado por Defecto de Bloque Syscheck]](img-SOC-Wazuh/27.png)

Para sincronizar la telemetría en tiempo real con VirusTotal, modificamos dicho bloque agregando las siguientes directivas avanzadas:

* `<alert_new_files>yes</alert_new_files>`: Obliga a Wazuh a generar de inmediato un evento analizable en cuanto detecta la creación física de un nuevo archivo en el sistema operativo.
* `<directories realtime="yes">/tmp</directories>` e `/home/ubuntu`: Enlaza la vigilancia segundo a segundo directamente a través del kernel, definiendo con exactitud qué carpetas críticas del entorno van a ser auditadas de forma ininterrumpida.

Tras realizar las modificaciones, el bloque FIM consolidado en los agentes remotos debe quedar estructurado así:

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

![[📸 Captura: Fichero Syscheck Modificado Completo]](img-SOC-Wazuh/28.png)

_Consolidar los cambios reiniciando los agentes remotos del entorno:_  
```bash
sudo systemctl restart wazuh-agent
```

### 7.3 Comprobación

Para validar la correcta operatividad de la infraestructura frente a malware, ejecutamos un test biológico descargando el estándar internacional **EICAR**, un fichero legítimo inofensivo que simula una cadena maliciosa y que todas las soluciones de seguridad del mercado identifican obligatoriamente como virus de prueba.

Accedemos por terminal a la consola local del servidor Honeypots e introducimos el binario de prueba dentro del directorio monitorizado en tiempo real `/tmp`:

_Descargar el binario de prueba de malware estandarizado EICAR:_  
```bash
wget -P /tmp [https://secure.eicar.org/eicar.com](https://secure.eicar.org/eicar.com)
```

![[📸 Captura: Ejecución de Descarga EICAR]](img-SOC-Wazuh/29.png)

En el mismo segundo en el que el archivo toca el almacenamiento físico, el bot de Discord debe procesar la telemetría enviada por Wazuh y desplegar la alerta roja de detección de amenaza en el canal operativo.

![[📸 Captura: Alerta Roja de VirusTotal Recibida en Discord]](img-SOC-Wazuh/30.png)

Para examinar en detalle el incidente desde la consola centralizada de Wazuh, navegamos hacia la sección de **Threat Hunting** y seleccionamos el agente correspondiente a los `Honeypots`.

![[📸 Captura: Navegación Dashboard de Amenazas]](img-SOC-Wazuh/31.png)

En este panel disponemos de la cronología de eventos de seguridad. Al acceder al registro crítico catalogado como **Nivel de riesgo 12**, visualizamos los detalles asociados a la firma del archivo descargado.

![[📸 Captura: Detalles de Registro Crítico Nivel 12]](img-SOC-Wazuh/32.png)

Si seleccionamos la pestaña comparativa *Top 5*, el motor nos despliega con exactitud el nombre del ejecutable malicioso interceptado y su ruta absoluta dentro del host de destino.

![[📸 Captura: Mapeo Top 5 de Amenaza e Ingesta de Rutas]](img-SOC-Wazuh/33.png)

---

## 8. Configuración de Dominio Dinámico Gratuito (Duck DNS)

Para profesionalizar el acceso a la consola de control de seguridad del SOC y suprimir la necesidad de recordar e introducir de manera expuesta la dirección IP de AWS (`https://3.229.242.100/`), recurrimos al despliegue de un enrutamiento por subdominio dinámico empleando los servicios de **Duck DNS**.

### 8.1 Registro en Duck DNS

En primer lugar, nos autenticamos en la plataforma web de Duck DNS mediante una cuenta autorizada para la generación del token único de control corporativo. Completado el registro, dispondremos en pantalla de las credenciales globales del entorno y de la fecha de expedición del servicio.

A continuación, reservamos el subdominio que utilizaremos de forma exclusiva para el proyecto, configurado en este caso bajo la dirección: **`wazuh-cyberarena`**.

![[📸 Captura: Generación de Registro y Dominio Duck DNS]](img-SOC-Wazuh/34.png)

Una vez reservada la dirección, el asistente despliega una casilla de enlace de red donde debemos inyectar la dirección IP pública de nuestra máquina virtual de AWS, forzando la redirección del tráfico de manera inmediata.

![[📸 Captura: Vinculación de IP de AWS en Registro]](img-SOC-Wazuh/35.png)

### 8.2 Instalación de Certbot y Obtención del Certificado SSL

Con el subdominio dinámico apuntando correctamente hacia nuestra máquina virtual en AWS, procedemos a la instalación de la herramienta criptográfica oficial **Certbot** para resolver con éxito el desafío web HTTP-01 y obtener un certificado SSL/TLS de producción firmado por la entidad de confianza internacional **Let's Encrypt**.

_Instalar el paquete cliente Certbot dentro del entorno Amazon Linux 2023:_  
```bash
sudo dnf install certbot -y
```

_Ejecutar la solicitud y generación del certificado oficial abriendo el puerto standalone 80:_  
```bash
sudo certbot certonly --standalone -d wazuh-cyberarena.duckdns.org
```

Durante la ejecución del asistente interactivo, introducimos el correo electrónico administrativo para notificaciones críticas, aceptamos las condiciones legales de la licencia presionando `Y` y declinamos el envío de publicidad externa. El cliente ACME completó las verificaciones con los servidores remotos y almacenó los archivos de claves de forma satisfactoria en la máquina host.

![[📸 Captura: Proceso Interactivo del Asistente Certbot 1]](img-SOC-Wazuh/36.png)  
![[📸 Captura: Proceso Interactivo del Asistente Certbot 2]](img-SOC-Wazuh/37.png)

---

## 9. Vinculación del Certificado SSL en Wazuh Dashboard

Con las claves criptográficas públicas (`fullchain.pem`) y privadas (`privkey.pem`) emitidas correctamente por una CA de confianza, reconfiguramos el servidor web interno encargado de gestionar la interfaz gráfica del SOC (OpenSearch Dashboards) para aplicar una navegación TLS robusta y habilitar el candado verde de conexión segura en el navegador de los analistas.

1. **Ajuste crítico de permisos UNIX:** Debido a que los certificados emitidos por Let's Encrypt se generan bajo la propiedad e inmunidad exclusiva del usuario `root`, resulta obligatorio conceder privilegios explícitos de lectura al grupo del sistema que arranca el entorno gráfico web para evitar que el servicio colapse por falta de acceso al iniciarse.

_Cambiar la propiedad del grupo de claves y aplicar permisos restrictivos 750 al directorio seguro:_  
```bash
sudo chown -R root:wazuh-dashboard /etc/letsencrypt/
sudo chmod -R 750 /etc/letsencrypt/
```

2. Abrimos el fichero principal de personalización del Dashboard web del SIEM:

_Editar los parámetros globales de la interfaz web gráfica:_  
```bash
sudo nano /etc/wazuh-dashboard/opensearch_dashboards.yml
```

3. Sustituimos las antiguas rutas que apuntaban a los certificados autofirmados por defecto del sistema e inyectamos la ubicación exacta de las claves criptográficas definitivas provistas por Let's Encrypt:

```yaml
# Archivo opensearch_dashboards.yml modificado de forma definitiva por nuestro grupo
server.host: 0.0.0.0
opensearch.hosts: [https://127.0.0.1:9200](https://127.0.0.1:9200)
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

![[📸 Captura: Modificación de Rutas Criptográficas en Fichero]](img-SOC-Wazuh/38.png)

_Reiniciar el servicio web para aplicar y levantar la interfaz bajo la nueva configuración segura:_  
```bash
sudo systemctl restart wazuh-dashboard
```

A partir de este momento, la consola del SOC central queda expuesta de forma segura bajo el dominio completo de producción: **`https://wazuh-cyberarena.duckdns.org/app/login`**

![[📸 Captura: Interfaz de Acceso del SOC Protegida con Candado SSL]](img-SOC-Wazuh/39.png)

---

## 10. Errores y Soluciones

### 10.1 Fallo de Validación del Contexto SSL en el Entorno Virtual Aislado de Python

El primer error fue que Wazuh ejecutar sus scripts de integración usando un entorno de python propio pero que esta aislado. El problema es que no comparte las certificaciones por lo que no podía verificar el certificado de Discord de forma segura al realizar las peticiones por HTTPS. Lo que hemos hecho es  importar la librería ssl y hemos deshabilitado la verificación estricta de los certificados

Esto lo hemos hecho modificando el archivo nano /var/ossec/integrations/slack.py y añadiendo ssl._create_default_https_context = ssl._create_unverified_context
```python
ssl._create_default_https_context = ssl._create_unverified_context
```

![[📸 Captura: Inyección de Parche SSL en Script de Python]](img-SOC-Wazuh/40.png)

### 10.2 Restricción de Permisos UNIX

El segundo error fue que las alertas de nivel alto se registraban dentro de /var/ossec/logs/alerts/alerts.json pero Wazuh no enviaba la información a Discord automáticamente.

Esto lo sabíamos porque al hacer pruebas de manera local si que enviaba la alerta. Lo solucionamos poniendo permisos a la raíz con **sudo chmod +x /var/ossec/integrations/slack**.

_Asignar atributos UNIX de ejecución sobre el archivo de integración de logs:_  
```bash
sudo chmod +x /var/ossec/integrations/slack
```

### 10.3 Degradación de Petición por Redirección HTTP con Pérdida de Payload

Este error consiste en las redirecciones silenciosas pero con pérdida de Payload. El problema aquí era que el script oficial de Python se ejecutaba y ponía que había sido exitoso pero no aparecía nunca nada en Discord.

El problema era que había cambiado el dominio, nosotros al principio estábamos poniendo el dominio **discordapp.com**. Y no debería ser un problema ya que todo lo que iba por el dominio antiguo se redirige al nuevo dominio el cual es **discord.com**.
Entonces nos daba un **200 OK** falso. Descubrimos que la petición original que era **POST** al dirigirse al nuevo dominio cambiaba por un **GET** por tanto por el camino se destruía y nunca llegaba.

La solución fue modificar la URL de Wazuh y que el envio fuese directamente a discord.com


### 10.4 Rechazo de Payload por Incompatibilidad de Formato en el Timestamp

Este error es el que más problemas nos dio ya que no sabíamos el porque fallaba en llegados a este punto.

Resulta que al hacer un ataque real Discord rechazaba la petición y nos daba el error **HTTP 400 Bad Request**.

Conseguimos averiguar que la causa era que Wazuh asigna una ID de alerta que se compone de números los cuales son un string con decimales (ejemplo: 1223344.45455435). 

Resulta que la API de Discord requiere estrictamente que los números sean Integer es decir que sean enteros y no decimales lo que causaba errores.

Para solucionar este problema tuvimos que editar el archivo /var/ossec/integrations/slack.py y modificar la linea **msg['ts'] = alert['id']** y cambiarla a **msg['ts'] = int(alert['id'].split('.')[0])** que hace lo siguiente.


_Código de parcheo inyectado en la variable temporal:_  
```python
msg['ts'] = int(alert['id'].split('.')[0])
```

#### Desglose del funcionamiento mecánico del parche en código:

| Porción de código | Qué hace en ese instante | Resultado técnico en el flujo |
| :--- | :--- | :--- |
| **`alert['id']`** | Extrae el identificador único string de la alerta del SIEM de Wazuh. | `"1778360885.149374"` *(Cadena de texto)* |
| **`.split('.')`** | Segmenta el texto en dos fragmentos independientes tomando el punto como separador. | `["1778360885", "149374"]` *(Estructura de lista)* |
| **`[0]`** | Selecciona exclusivamente el primer fragmento de la lista (el contenido previo al punto). | `"1778360885"` *(Cadena de texto limpia)* |
| **`int(...)`** | Transforma la cadena de texto filtrada en un valor entero numérico puro (Integer). | `1778360885` *(Valor numérico entero)* |
| **`msg['ts'] =`** | Inyecta el entero en el parámetro temporal esperado por los servidores remotos. | Envío de Payload válido compatible con la API de Discord. |****
