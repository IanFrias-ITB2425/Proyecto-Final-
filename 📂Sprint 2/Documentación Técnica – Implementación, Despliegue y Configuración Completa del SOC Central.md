# **Documentación Técnica – Implementación, Despliegue y Configuración Completa del SOC Central**

**Proyecto:** CyberArena – Plataforma de Ciberseguridad Centralizada

**Proveedor Cloud:** Amazon Web Services (AWS)

**Sistema Operativo Base:** Amazon Linux 2023 (Kernel 6.1) / Nodos Cliente en Ubuntu 24.04 LTS

**Fecha de despliegue:** Mayo 2026

---

## 

## **Índice**

## 

## 

## 

## 

## **4\. Instalación de la Arquitectura Central de Wazuh SIEM**

Una vez dentro de la terminal de AWS, procedimos con la descarga e instalación del motor centralizado de Wazuh.

Bash  
\# Descargar el instalador  
 **curl \-sO https://packages.wazuh.com/4.14/wazuh-install.sh && sudo bash ./wazuh-install.sh \-a**

Una vez finalizada la descarga y desompresion de los archivos el instalador nos proporcionó las credenciales de acceso del perfil administrativo, las cuales utilizariamos para acceder a Wazuh y poder gestionarlo:

* **Usuario maestro:** admin  
* **Contraseña generada:** M7xRS1KfhjYHlu?PSpB45NaT9UQ490Sb

A partir de este momento, comprobamos que el clúster respondía tecleando la dirección IP asignada directamente en la barra del navegador: [https://3.229.242.100/](https://3.229.242.100/).

![][image1]

## **5\. Despliegue de la Red Privada Virtual Cifrada (Wireguard VPN)**

Como medida de confidencialidad para que los logs de auditoría interna de nuestras máquinas no viajen expuestos a internet, configuramos una red privada virtual (VPN) ligera y de alta velocidad basada en el protocolo **Wireguard**.

Para ello instalamos wireward con la comanda **sudo dnf install wireguard-tools \-y**

![][image2]

Bash  
Ahora habia que generar las credenciales:

\# Generar la pareja de claves criptográficas asimétricas en el servidor maestro  
**wg genkey | tee privatekeywazuh | wg pubkey \> publickeywazuh**

Creamos el archivo de control de la interfaz virtual /etc/wireguard/wg0.conf asignando las siguientes directivas de enrutamiento seguro:

**\[Interface\]**  
**Address \= 10.7.0.2/24**  
**PrivateKey \= eNR1/7urU7JNsHj32bOpgavewfK9+hqmq3IkmaLlLG0=**  
**MTU \= 1420**

**\[Peer\]**  
**PublicKey \= Xna/Ukx43zduP4algQ/IbeJTL/FWSbaMY6UkAadowlg=**

**Endpoint \= 10.0.1.175:51820**         

**AllowedIPs \= 10.7.0.0/24**  
**PersistentKeepalive \= 25**

![][image3]

**Solución a error de enrutamiento:** Al levantar la interfaz virtual se detectó el fallo crítico iptables-restore: command not found debido a que Amazon Linux 2023 carece de herramientas de red obsoletas. 

Corregimos esta incidencia instalando el traductor NFTables moderno ejecutando: 

sudo dnf install iptables-nft \-y.

## **6\. Instalación y Alta de Agentes de Seguridad Distribuidos**

Con el servidor maestro escuchando peticiones de red, entramos en las consolas de nuestros dos servidores (server-web, honeypots) para poder hacer que el agente local de Wazuh pueda recolectar los eventos internos de cada máquina.

Para ello hay que entrar en Wazuh. Dentro de él accederemos al desplegable lateral y entraremos dentro de **Server Manager**, en él aparecerá un desplegable y tendremos que entrar en la opción **Endpoints Summary**.

![][image4]

Una vez dentro le daremos al botón **Deploy new agent**.

**![][image5]**

### **6.1 Servidor Web**

La integración de Wazuh a cada máquina es personalizada y única aunque el proceso es parecido.

Para el servidor web como sistema que elegimos es un con Linux DEB amd64, escogemos este porque el servidor web está montado en un Ubuntu Desktop

![][image6]

En server address hay que poner la IP de la máquina donde van a llegar todos los logs. Por tanto es 3.229.242.100.

![][image7]

En **optional settings** podemos asignar un nombre al agente como estamos en la configuración de servidor web lo hemos llamado **Servidor-Web**. Hay que tener en cuenta que a partir de este nombre ningún agente se podrá llamar así.

![][image8]

Ahora desde la máquina que queremos monitorear en este caso el servidor web ejecutamos la siguiente comanda personalizada que nos ha generado Wazuh gracias a los datos que hemos puesto.

Bash  
\# Descargar el paquete wget https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent\_4.9.2-1\_amd64.deb && sudo WAZUH\_MANAGER='3.229.242.100' WAZUH\_AGENT\_NAME='Servidor-Web' dpkg \-i ./wazuh-agent\_4.9.2-1\_amd64.deb  
   
\# Comandas una vez instalado los paquetes  
sudo systemctl daemon-reload  
sudo systemctl enable wazuh-agent  
sudo systemctl start wazuh-agent  
   
Una vez hecho estos pasos en Wazuh deberá aparecer un Endpoint llamado Servidor-Web

![][image9]

### **6.2 Servidor Honeypot**

En este servidor el proceso será el mismo pero la comanda resultante será diferente. Por tanto los parámetros hay que adaptarlos a este servidor.

Selección de S.O

![][image6]

Server address:  
![][image6]

Como nombre de la máquina hemos optado a llamarla Honeypot ya que es la que aloja estos servicios para poder confundir a los atacantes.

## 

![][image10]

Ahora una vez seleccionado los datos la comanda resultante es la siguiente, la cual se correrá dentro de la máquina que tiene alojado los honeypots.

Bash  
\# Descargar el paquete wget  
wget https://packages.wazuh.com/4.x/apt/pool/main/w/wazuh-agent/wazuh-agent\_4.9.2-1\_amd64.deb && sudo WAZUH\_MANAGER='3.229.242.100' WAZUH\_AGENT\_NAME='Honeypots' dpkg \-i ./wazuh-agent\_4.9.2-1\_amd64.deb

\# Comandas una vez instalado los paquetes  
sudo systemctl daemon-reload  
sudo systemctl enable wazuh-agent  
sudo systemctl start wazuh-agent

Una vez ejecutada las comandas dentro del Wazuh veremos que se ha añadido

![][image11]  
![][image12]

## **7\. Extracción Activa de Logs de Contenedores Docker**

Para dotar al nodo **Servidor Honeypots** de una superficie de ataque realista y atractiva para los atacantes de internet, desplegamos mediante contenedores Docker aislados las aplicaciones vulnerables de referencia en auditorías: **OWASP Juice Shop** y **DVWA**. Como Docker encapsula sus registros y bloquea el acceso externo, programamos un script extractor en segundo plano.

Bash  
\# Guardar los registros de Docker en archivos locales en tiempo real  
sudo sh \-c 'nohup docker logs \-f cyberarena\_juiceshop \> /var/log/juiceshop.log 2\>&1 &'  
sudo sh \-c 'nohup docker logs \-f cyberarena\_dvwa \> /var/log/dvwa.log 2\>&1 &'

Abrimos el archivo /var/ossec/etc/ossec.conf dentro del servidor Honeypot y añadimos los bloques de al final para canalizar ese tráfico hacia nuestro manager de AWS:

 \<localfile\>  
    \<log\_format\>syslog\</log\_format\>  
    \<location\>/var/log/juiceshop.log\</location\>  
  \</localfile\>

  \<localfile\>  
    \<log\_format\>syslog\</log\_format\>  
    \<location\>/var/log/dvwa.log\</location\>  
  \</localfile\>

![][image13]

## **8\. Configuración del Motor de Integridad de Archivos en Tiempo Real**

Para vigilar con precisión absoluta e instantánea la aparición de troyanos, *webshells* de persistencia o alteraciones ilegítimas en nuestros archivos web, modificamos de forma manual el motor **Syscheck FIM** en ambas máquinas cliente.

### **Líneas integradas dentro del archivo ossec.conf local del Servidor-Web:**

XML  
 \<syscheck\>  
    \<disabled\>no\</disabled\>  
    \<frequency\>43200\</frequency\>  
    \<scan\_on\_start\>yes\</scan\_on\_start\>

    \<alert\_new\_files\>yes\</alert\_new\_files\>

    \<directories realtime="yes"\>/tmp\</directories\>  
    \<directories realtime="yes"\>/var/www/html\</directories\>  
  \</syscheck\>

### **Líneas integradas dentro del archivo ossec.conf local del Servidor Honeypots:**

 \<syscheck\>  
    \<disabled\>no\</disabled\>  
    \<frequency\>43200\</frequency\>  
    \<scan\_on\_start\>yes\</scan\_on\_start\>  
    \<alert\_new\_files\>yes\</alert\_new\_files\>

    \<directories realtime="yes"\>/tmp\</directories\>  
    \<directories realtime="yes"\>/home/ubuntu\</directories\>  
  \</syscheck\>

**Justificación técnica de seguridad:** Activando realtime="yes", los agentes se enlazan directamente con las llamadas de interrupción del kernel de Linux (inotify). Esto provoca el envío inmediato del hash SHA256 al servidor centralizado en el mismo milisegundo en el que un archivo toca el almacenamiento, anulando la ventana de exposición de 12 horas establecida por defecto.

*Aplicamos los cambios reiniciando los dos agentes remotos: sudo systemctl restart wazuh-agent.*

## 

## **9\. Integración de API de Webhooks de Discord**

Para conseguir visibilidad total e instantánea ante cualquier de ataque detectado, programamos una integración para desviar de manera automatizada cualquier incidente de **Nivel de riesgo igual o superior a 7** hacia nuestro canal corporativo de operaciones de Discord.

### **Pasos de Configuración en el Servidor Central (AWS):**

1. Creamos el canal de texto \#alertas-wazuh en nuestro servidor privado de Discord y generamos un Webhook de integración.

   **![][image14]**  
   **![][image15]**

   Seleccionamos un nombre, en este caso se llamará **SOC \- Wazuh Alerts**   
   ![][image16]

   Ahora añadimos un nuevo canal el cual llamaremos **alertas-wazuh.** Para ello en el canal de texto llamado **\# general** le damos al botón **\+** y nos aparecerá las opciones para la creación del canal.

   En este caso la configuración es poner el tipo de canal en **texto** y el nombre del canal. En caso de ser una empresa podremos seleccionar la opción de **Canal Privado**, esto lo que haría es que solo las personas seleccionadas por el administrador del servidor puede dejar acceder al canal que vamos a crear a las personas que él seleccione, por tanto un empleado de otro departamento no podría acceder a las alertas.

   ![][image17]

Después de la creación del canal hay que generar el **Webhook** que es lo que hará conexión entre wazuh y Discord.

Para ello entramos a la configuración del canal y tenemos que entrar a la opción de **Integración.**  
**![][image18]**  
![][image19]

Una vez dentro hacemos click en Nuevo Webhook y lo llamamos **Wazuh Bot,** copiamos la URL de webhook y guardamos los cambios.  
![][image20]

2. **El bypass:** Como Wazuh cuenta con integración nativa madura para Slack pero carece de conector directo para Discord, aprovechamos la pasarela de compatibilidad de Discord **agregando el sufijo /slack al final de la URL** generada.

3. Añadimos el bloque definitivo abriendo el archivo /var/ossec/etc/ossec.conf de nuestro **Wazuh Manager** en AWS:

bash  
 \<integration\>  
    \<name\>slack\</name\>  
    \<level\>7\</level\>  
    \<hook\_url\>https://discord.com/api/webhooks/1502728258163314859/Sf4Qx2rJbq3NjMX0e3l1CmobEBiOHf\_Yoe5EMVPngo8Ngu0aPHyAm1KyXh8OupP0VB3z/slack\</hook\_url\>  
    \<alert\_format\>json\</alert\_format\>  
  \</integration\>

**![][image21]**

## **10\. Conexión con la API de Inteligencia Antivirus de VirusTotal**

La segunda API Key que integramos es la de VirusTotal. Gracias a esta API Key si alguien intenta introducir o un virus o ha descargado un virus dentro de los servicios y es algún virus conocido por la base de datos de VirusTotal lo detectará y nos enviará un aviso tanto a Wazuh como al Discord.

Si por otra parte el hacker es el que crea su propio exploit y no es conocido por VirusTotal no lo detectara, pero Wuazuh si lo hará.

Lo interesante es que Wazuh tiene integración nativa con VirusTotal por tanto será mucho más fácil aplicar este servicio.

### **10.1 Registro en Virustotal**

Lo primero que haremos es entrar a [virustotal.com](http://virustotal.com) y registrarnos. Una vez registrados podremos copiar la API Key.

![][image22]

Dentro de la sección de API Key podremos ver datos como las veces que detectará archivos y el recuento de veces que ha escaneado

![][image23]

### **10.2. Implementación en Wazuh**

Ahora con la API Key copiada tenemos que dirigirnos a **nano /var/ossec/etc/ossec.conf** y configuramos el archivo añadiendo abajo del todo lo siguiente junto la API Key

 \<virustotal\>  
    \<enabled\>yes\</enabled\>  
    \<api\_key\>d7512cfed73a44b500d86ed47b820092877cc86ff91874f0cc005a4b66b5325c\</api\_key\>  
    \<group\>syscheck\</group\>  
    \<alert\_format\>json\</alert\_format\>  
  \</virustotal\>  
![][image24]

Una vez aplicado el cambio hacemos un restart sudo systemctl restart wazuh-manager.

Ahora en las máquinas que queremos vigilar como es el Servidor Web y los Honeypots tendremos que hacer lo siguiente.

Hay que editar el archivo **/var/ossec/etc/ossec.conf**  dentro de él tendremos que editar la etiqueta syscheck. De serie viene de esta manera

  **\<syscheck\>**  
    **\<disabled\>no\</disabled\>**

    **\<frequency\>43200\</frequency\>**

    **\<scan\_on\_start\>yes\</scan\_on\_start\>**

    **\<directories\>/etc,/usr/bin,/usr/sbin\</directories\>**  
    **\<directories\>/bin,/sbin,/boot\</directories\>**

![][image25]

A este bloque hay que añadirle las siguientes líneas

**\<alert\_new\_files\>yes\</alert\_new\_files\>**

Le dice a Wazuh que genere una alerta e inicie el análisis de integridad en cuanto se cree un archivo nuevo.

**\<directories realtime="yes"\>/tmp\</directories\> y /home/ubuntu**   
Activa la vigilancia en tiempo real utilizando el kernel de Linux

Define qué carpetas exactas de la máquina vamos a monitorizar segundo a segundo.   
Una vez añadidas el archivo queda de la siguiente manera:

 **\<syscheck\>**  
    **\<disabled\>no\</disabled\>**

    **\<frequency\>43200\</frequency\>**

    **\<scan\_on\_start\>yes\</scan\_on\_start\>**

    **\<directories\>/etc,/usr/bin,/usr/sbin\</directories\>**  
    **\<directories\>/bin,/sbin,/boot\</directories\>**

    **\<alert\_new\_files\>yes\</alert\_new\_files\>**

    **\<directories realtime="yes"\>/tmp\</directories\>**  
    **\<directories realtime="yes"\>/home/ubuntu\</directories\>**

    **\<ignore\>/etc/mtab\</ignore\>**  
    **\<ignore\>/etc/hosts.deny\</ignore\>**  
    **\<ignore\>/etc/mail/statistics\</ignore\>**  
    **\<ignore\>/etc/random-seed\</ignore\>**  
    **\<ignore\>/etc/random.seed\</ignore\>**  
    **\<ignore\>/etc/adjtime\</ignore\>**

![][image26]

**sudo systemctl restart wazuh-agent**

### 

### **10.3. Comprobación**

Ahora comprobaremos si ha funcionado, para ello descargamos el archivo **EICAR** que es famoso por ser un archivo que se detecta como virus aunque no tiene ningún peligro.

Para ello dentro del la terminal de los Honeypots haremos lo siguiente

**wget \-P /tmp [https://secure.eicar.org/eicar.com](https://secure.eicar.org/eicar.com)**

![][image27]

Ahora en el Discord nos debería de haber saltado el aviso de la descarga del archivo malicioso.

![][image28]

Con esto comprobamos que la implementación de la API Key de VirusTotal ha sido exitosa

Para verlo en Wazuh vamos a Threat Hunting y en este caso iremos al servidor Honeypots

![][image29]

Aquí tenemos todos los avisos y podemos ver un resumen de que es cada una. Si entramos donde nos avisa de un **level 12** encontraremos lo siguiente.

![][image30]

Si seleccionamos el Top 5 nos aparecera el archivo malicioso y la ruta.

![][image31]

## **11\. Configuración de Dominio Dinámico Gratuito (Duck DNS)**

Para profesionalizar el acceso a la consola de control de seguridad, suprimir el uso de direcciones IP expuestas ([https://3.229.242.100/](https://3.229.242.100/)) recurrimos al despliegue de un direccionamiento dinámico con **Duck DNS**.

## **11.1 Registro en Ducks DNS**

Lo primero que haremos es registrarnos en la web Duck DNS para poder obtener el token.  
Una vez registrados con una cuenta de google nos aparecerá información como la cuenta usada, el token y el día generado.

Lo que nos interesa de esa página es que ahora podremos poner el nombre del dominio que queremos poner, en nuestro caso wazuh-cyberarena.

![][image32]

Una vez puesto el nombre de dominio nos saltará otro apartado donde tendremos que poner la ip que queremos cambiar por el nombre del dominio.

![][image33]

## 

### **11.2 Instalación de Certbot y Obtención del Certificado SSL**

Con el subdominio dinámico enrutando correctamente el tráfico hacia nuestra máquina virtual, instalamos la utilidad cliente ACME oficial **Certbot** para completar el desafío criptográfico HTTP-01 y obtener un certificado SSL/TLS real firmado por la entidad emisora internacional **Let's Encrypt**.

Bash  
\# Paso 1: Instalar el paquete cliente Certbot dentro de nuestro entorno Amazon Linux 2023  
**sudo dnf install certbot \-y**

\# Paso 2: Ejecutar la solicitud del certificado oficial mediante el puerto web 80  
sudo certbot certonly \--standalone \-d wazuh-cyberarena.duckdns.org

Durante el asistente en línea, inyectamos nuestro correo electrónico para notificaciones de seguridad, aceptamos las condiciones de licenciamiento presionando Y y denegamos el envío de publicidad. El sistema se comunicó con los servidores de validación y almacenó las llaves criptográficas de confianza de forma satisfactoria en la máquina.

![][image34]  
![][image35]

## **13\. Vinculación del Certificado SSL en Wazuh Dashboard**

Con las claves criptográficas públicas y privadas ya emitidas, configuramos el servidor web interno de Wazuh (OpenSearch Dashboards) para que empiece a servir la interfaz cifrada bajo nuestro nuevo dominio seguro, activando el candado verde de conexión en el navegador.

1. **Ajuste crítico de permisos UNIX:** Como los certificados de Let's Encrypt se crean bajo propiedad exclusiva del usuario root, concedimos permisos explícitos de lectura al grupo que ejecuta el proceso web para evitar bloqueos del servicio al arrancar:

Bash  
sudo chown \-R root:wazuh-dashboard /etc/letsencrypt/  
sudo chmod \-R 750 /etc/letsencrypt/

2. Abrimos el archivo de configuración de la interfaz gráfica web:

Bash  
sudo nano /etc/wazuh-dashboard/opensearch\_dashboards.yml

3. Modificamos los punteros SSL antiguos y autofirmados por nuestras nuevas rutas criptográficas de confianza emitidas por Let's Encrypt:

YAML  
\# Archivo opensearch\_dashboards.yml modificado de forma definitiva por nuestro grupo  
server.host: 0.0.0.0  
opensearch.hosts: https://127.0.0.1:9200  
server.port: 443  
opensearch.ssl.verificationMode: certificate  
opensearch.requestHeadersAllowlist: \["securitytenant","Authorization"\]  
opensearch\_security.multitenancy.enabled: false  
opensearch\_security.readonly\_mode.roles: \["kibana\_read\_only"\]  
server.ssl.enabled: true  
server.ssl.key: "/etc/letsencrypt/live/wazuh-cyberarena.duckdns.org/privkey.pem"  
server.ssl.certificate: "/etc/letsencrypt/live/wazuh-cyberarena.duckdns.org/fullchain.pem"  
opensearch.ssl.certificateAuthorities: \["/etc/wazuh-dashboard/certs/root-ca.pem"\]  
uiSettings.overrides.defaultRoute: /app/wz-home  
opensearch\_security.cookie.secure: true

**![][image36]**

*Reiniciamos el backend web para levantar los cambios: sudo systemctl restart wazuh-dashboard*

**https://wazuh-cyberarena.duckdns.org/app/login?**

**![][image37]**

## **14\. Errores y Soluciones**

### **14.1. Fallo de Validación del Contexto SSL en el Entorno Virtual Aislado de Python** 

El primer error fue que Wazuh ejecutar sus scripts de integración usando un entorno de python propio pero que esta aislado. El problema es que no comparte las certificaciones por lo que no podía verificar el certificado de Discord de forma segura al realizar las peticiones por HTTPS. Lo que hemos hecho es  importar la librería ssl y hemos deshabilitado la verificación estricta de los certificados

Esto lo hemos hecho modificando el archivo **nano /var/ossec/integrations/[slack.py](http://slack.py)** y añadiendo **ssl.\_create\_default\_https\_context \= ssl.\_create\_unverified\_context**

## **![][image38]**

## **14.2. Restricción de Permisos UNIX** 

## El segundo error fue que las alertas de nivel alto se registraban dentro de /var/ossec/logs/alerts/alerts.json pero Wazuh no enviaba la información a Discord automáticamente.

Esto lo sabíamos porque al hacer pruebas de manera local si que enviaba la alerta. Lo solucionamos poniendo permisos a la raíz con **sudo chmod \+x /var/ossec/integrations/slack**.

### **14.3. Degradación de Petición por Redirección HTTP con Pérdida de Payload**

Este error consiste en las redirecciones silenciosas pero con pérdida de Payload. El problema aquí era que el script oficial de Python se ejecutaba y ponía que había sido exitoso pero no aparecía nunca nada en Discord.

El problema era que había cambiado el dominio, nosotros al principio estábamos poniendo el dominio [**discordapp.com**](http://discordapp.com). Y no debería ser un problema ya que todo lo que iba por el dominio antiguo se redirige al nuevo dominio el cual es [**discord.com**](http://discord.com).  
Entonces nos daba un **200 OK** falso. Descubrimos que la petición original que era **POST** al dirigirse al nuevo dominio cambiaba por un **GET** por tanto por el camino se destruía y nunca llegaba.

La solución fue modificar la URL de Wazuh y que el envio fuese directamente a [discord.com](http://discord.com)

**14.4. Rechazo de Payload (HTTP Error 400 Bad Request) por Incompatibilidad de Formato en el Timestamp** 

Este error es el que más problemas nos dio ya que no sabíamos el porque fallaba en llegados a este punto.

Resulta que al hacer un ataque real Discord rechazaba la petición y nos daba el error **HTTP 400 Bad Request**.

Conseguimos averiguar que la causa era que Wazuh asigna una ID de alerta que se compone de números los cuales son un string con decimales (ejemplo: 1223344.45455435). 

Resulta que la API de Discord requiere estrictamente que los números sean Integer es decir que sean enteros y no decimales lo que causaba errores.

Para solucionar este problema tuvimos que editar el archivo **/var/ossec/integrations/slack.py** y modificar la linea **msg\['ts'\] \= alert\['id'\]** y cambiarla a **msg\['ts'\] \= int(alert\['id'\].split('.')\[0\])** que hace lo siguiente.

| Porción de código | Qué hace en ese instante | Resultado en nuestro ejemplo |
| :---- | :---- | :---- |
| **alert\['id'\]** | Obtiene el identificador único de la alerta de Wazuh (que viene con decimales). | "1778360885.149374" *(un texto)* |
| **.split('.')** | Corta el texto en dos partes usando el punto . como tijera. | \["1778360885", "149374"\] *(una lista)* |
| **\[0\]** | Selecciona únicamente el primer elemento de la lista (lo que hay a la izquierda del punto). | "1778360885" *(el timestamp en segundos)* |
| **int(...)** | Convierte ese texto en un número entero puro (Integer), quitándole las comillas. | 1778360885 *(un número)* |
| **msg\['ts'\] \=** | Guarda ese número entero en el parámetro 'ts' (Timestamp) que se le enviará a Discord. | El mensaje ahora tiene un tiempo válido para la API. |

