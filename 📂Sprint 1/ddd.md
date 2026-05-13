# **Documentación Técnica – Implementación, Despliegue y Configuración Completa del SOC Central**

**Proyecto:** CyberArena – Plataforma de Ciberseguridad Centralizada  
**Proveedor Cloud:** Amazon Web Services (AWS)  
**Sistema Operativo Base:** Amazon Linux 2023 (Kernel 6.1) / Nodos Cliente en Ubuntu 24.04 LTS  
**Fecha de despliegue:** Mayo 2026  

---

## 📋 Índice

* [1. AWS (EC2 - Wazuh Manager)](#1-aws-ec2---wazuh-manager)
* [2. Configuración de Seguridad Perimetral (Security Group)](#2-configuración-de-seguridad-perimetral-security-group)
* [3. Conexión Remota vía SSH](#3-conexión-remota-vía-ssh)

---

## 1. AWS (EC2 - Wazuh Manager)

Para implementar el cerebro de nuestro SOC (Security Operations Center), desplegamos una instancia virtual en la infraestructura de Amazon Web Services (AWS). Esta máquina actúa como el nodo maestro centralizador encargado de procesar la telemetría, indexar los logs entrantes de toda nuestra red y coordinar las respuestas automatizadas ante incidentes.

**Especificaciones de la instancia central:**

| Parámetro | Valor |
| :--- | :--- |
| Tipo de instancia | t3.medium *(Escalado dinámico a t3.large por recursos)* |
| Sistema operativo | Amazon Linux 2023 (AMI Oficial) |
| Estado de ejecución | En ejecución ✅ |
| Arquitectura de procesamiento | x86_64 |
| Almacenamiento base | Volumen EBS (Elastic Block Store) SSD de 30 GB |

**Nota sobre el incidente de hardware solucionado:** Durante la primera fase de compilación e instalación del motor indexador, detectamos una congelación total del sistema debido a la falta de espacio y desbordamiento de memoria RAM en entornos menores de 15 GB de disco. Para corregirlo de inmediato, aplicamos un escalado vertical pasando la instancia temporalmente a una tipología t3.large y ampliando el almacenamiento en bloque hasta los 30 GB SSD.

![Instancia Central AWS](Sprint%201/Imagenes/wazuh/1.png)

---

## 2. Configuración de Seguridad Perimetral (Security Group)

A nivel de red perimetral, definimos las reglas del **Security Group** asociado a nuestro Wazuh Manager. Este actúa como un cortafuegos virtual a nivel de red, aplicando de forma predeterminada políticas restrictivas de denegación y controlando de manera minuciosa el tráfico de entrada (*inbound*) y salida (*outbound*) de la instancia.

### Configuración de puertos

* **Puerto 22 (TCP):** Para conexiones de administración remota por SSH.  
* **Puerto 443 (TCP):** Para acceder al panel web gráfico (Dashboard) de Wazuh.  
* **Puerto 1514 (TCP):** Puerto seguro para que los agentes remotos envíen sus logs al servidor.  
* **Puerto 1515 (TCP):** Puerto para el registro e intercambio de llaves de nuevos agentes.  
* **Puerto 55000 (TCP):** Para la comunicación interna con la API de Wazuh.  
* **Reglas de salida (Outbound):** Configuradas por **default a 0.0.0.0/0** para permitir actualizaciones e integraciones externas.  

![Security Group AWS](Sprint%201/Imagenes/wazuh/2.png)

---

## 3. Conexión Remota vía SSH

El acceso para la administración interna de la máquina virtual se realiza desde el terminal local mediante el protocolo cifrado **SSH**, utilizando autenticación robusta basada en la llave privada asimétrica .pem generada en el momento de crear la instancia en AWS.

**Procedimiento técnico de conexión:**

_Asignar permisos UNIX estrictos para que el sistema acepte la clave privada:_  
```bash  
chmod 400 sockey.pem