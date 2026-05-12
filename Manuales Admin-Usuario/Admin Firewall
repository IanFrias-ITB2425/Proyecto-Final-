# 🔧 Manual de Administrador: Infraestructura CyberArena

Este manual está destinado al equipo de Sistemas (Arquitecto / Defense Engineer) para el mantenimiento de la red.

## 1. Gestión de IPs y DNS
Debido al entorno de AWS Academy, el laboratorio apaga las máquinas diariamente.
* [cite_start]**Elastic IP:** El Router/Firewall tiene fijada la IP `100.29.105.237`[cite: 981]. Sin embargo, si el laboratorio se destruye, deberá solicitarse una nueva IP y asociarla a la interfaz `eth0` de la instancia.
* **Actualización del endpoint VPN:** Si la IP pública cambia, se debe editar el archivo de configuración en los clientes de WireGuard (campo `Endpoint = NUEVA_IP:51820`).

## 2. Gestión de Usuarios de VPN (WireGuard)
Para dar acceso a un nuevo miembro del equipo o profesor a la subred privada:
1. Acceder por SSH al Router Perimetral:
   [cite_start]`ssh -i "claves-pfsense.pem" ubuntu@100.29.105.237` [cite: 1001]
2. Ejecutar el script instalador:
   [cite_start]`sudo bash wireguard-install.sh` [cite: 1296]
3. Seleccionar la opción para añadir un nuevo cliente y darle un nombre (ej. `profesor`).
4. [cite_start]Extraer el archivo `.conf` generado en `/home/ubuntu/` [cite: 1200] y entregárselo al usuario de forma segura.

## 3. Revisión del Enrutamiento NAT
Si las máquinas de la subred privada pierden acceso a Internet:
1. [cite_start]Comprobar que el reenvío de IP sigue activo: `cat /proc/sys/net/ipv4/ip_forward` (debe devolver `1`)[cite: 1035, 1313].
2. Comprobar las reglas de iptables: `sudo iptables -t nat -L -v -n`. [cite_start]Debe existir la regla `MASQUERADE` para la red `10.0.2.0/24`[cite: 1312, 1323].
3. [cite_start]Comprobar en la consola de AWS EC2 que la instancia tiene desactivado el "Source/Destination Check"[cite: 1066, 1073].
