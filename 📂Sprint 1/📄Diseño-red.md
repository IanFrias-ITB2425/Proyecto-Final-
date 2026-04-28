# 🛡️ Configuración de Red Perimetral y Firewall (pfSense)

## 🌐 1. Arquitectura de Red en AWS
Para este proyecto, se ha diseñado una infraestructura de red robusta y segmentada dentro de una **VPC (Virtual Private Cloud)** personalizada. El objetivo es garantizar que los activos críticos estén aislados del tráfico directo de Internet.

### 📍 Segmentación de Subredes:
* **Subred Pública (10.0.1.0/24):** Zona DMZ donde reside el Firewall y servicios que requieren exposición controlada.
* **Subred Privada (10.0.2.0/24):** Zona blindada para el laboratorio de víctimas y servidores de datos.

![Arquitectura de Red CyberArena](assets/arquitectura-vpc.png)
*Figura 1: Esquema de red con segmentación de subredes y flujo de tráfico.*

---

## ⚙️ 2. Despliegue de pfSense Plus
Se ha utilizado una instancia **t3.small** para ejecutar pfSense, actuando como el cerebro de seguridad de nuestra red.

### ✅ Hitos de Configuración:
1.  **Gateway NAT:** Permite que las máquinas en la subred privada descarguen actualizaciones de seguridad sin tener una IP pública.
2.  **Reglas de Firewall:** Filtrado estricto de paquetes entrantes y salientes.
3.  **Gestión de Accesos:** Uso de llaves RSA seguras para la administración remota.

![Consola de AWS VPC](assets/consola-aws.png)
*Figura 2: Verificación de los recursos de red desplegados en la región de Virginia.*

---

## 🥷 3. Seguridad Stealth (Wireguard)
Para evitar ataques de fuerza bruta en los puertos de administración, hemos implementado la tecnología **Cripto-enrutamiento silencioso**.

* **Puerto:** 51820 (UDP).
* **Funcionamiento:** Si un escáner de puertos intenta conectar sin la llave criptográfica correcta, el servidor no responde (modo invisible).

![Esquema Stealth Mode](assets/stealth-config.png)
*Figura 3: Funcionamiento del túnel seguro Wireguard.*
