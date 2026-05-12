# 👤 Manual de Usuario: Conexión al Laboratorio CyberArena

Este manual explica cómo conectarse de forma segura a la red privada (Búnker) del proyecto CyberArena utilizando WireGuard VPN.

## 1. Requisitos Previos
* Descargar el cliente de WireGuard desde la web oficial (disponible para Windows, macOS, Linux, Android e iOS).
* Disponer del archivo de configuración `.conf` proporcionado por el Administrador de Sistemas de CyberArena (ej. `izan.conf`).

## 2. Pasos para Conectarse
1. Abre la aplicación de WireGuard en tu equipo.
2. Haz clic en **"Añadir túnel"** o **"Importar túnel desde un archivo"**.
3. Selecciona el archivo `.conf` que te ha proporcionado el administrador.
4. Haz clic en el botón **"Activar"** o **"Conectar"**.

## 3. Verificación de Conectividad
Una vez conectado, tu equipo formará parte virtual de la infraestructura Cloud.
* Para verificarlo, abre una terminal (CMD o PowerShell) y haz ping a una máquina interna, por ejemplo el Honeypot:
  `ping 10.0.2.X`
* Si obtienes respuesta, ya puedes acceder a los paneles web internos (como el SIEM de Wazuh o los contenedores Docker) abriendo el navegador y escribiendo su IP privada directamente.
