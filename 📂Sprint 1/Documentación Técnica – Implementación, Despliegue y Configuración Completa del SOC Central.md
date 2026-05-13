# **Documentación Técnica – Implementación, Despliegue y Configuración Completa del SOC Central**

**Proyecto:** CyberArena – Plataforma de Ciberseguridad Centralizada

**Proveedor Cloud:** Amazon Web Services (AWS)

**Sistema Operativo Base:** Amazon Linux 2023 (Kernel 6.1) / Nodos Cliente en Ubuntu 24.04 LTS

**Fecha de despliegue:** Mayo 2026

---

## 

## Índice

[**Documentación Técnica – Implementación, Despliegue y Configuración Completa del SOC Central	1**](#documentación-técnica-–-implementación,-despliegue-y-configuración-completa-del-soc-central)
1. [AWS (EC2 - Wazuh Manager)](#1-aws-ec2---wazuh-manager)
2. [Configuración de Seguridad Perimetral (Security Group)](#2-configuración-de-seguridad-perimetral-security-group)
3. [Conexión Remota vía SSH](#3-conexión-remota-vía-ssh)
## 

## 

## **1\. AWS (EC2 \- Wazuh Manager)**

Para implementar el cerebro de nuestro SOC (Security Operations Center), desplegamos una instancia virtual en la infraestructura de Amazon Web Services (AWS). Esta máquina actúa como el nodo maestro centralizador encargado de procesar la telemetría, indexar los logs entrantes de toda nuestra red y coordinar las respuestas automatizadas ante incidentes.

**Especificaciones de la instancia central:**

| Parámetro | Valor |
| :---- | :---- |
| Tipo de instancia | t3.medium *(Escalado dinámico a t3.large por recursos)* |
| Sistema operativo | Amazon Linux 2023 (AMI Oficial) |
| Estado de ejecución | En ejecución ✅ |
| Arquitectura de procesamiento | x86\_64 |
| Almacenamiento base | Volumen EBS (Elastic Block Store) SSD de 30 GB |

**Nota sobre el incidente de hardware solucionado:** Durante la primera fase de compilación e instalación del motor indexador, detectamos una congelación total del sistema debido a la falta de espacio y desbordamiento de memoria RAM en entornos menores de 15 GB de disco. Para corregirlo de inmediato, aplicamos un escalado vertical pasando la instancia temporalmente a una tipología t3.large y ampliando el almacenamiento en bloque hasta los 30 GB SSD.

![][image1]

## **2\. Configuración de Seguridad Perimetral (Security Group)**

A nivel de red perimetral, definimos las reglas del **Security Group** asociado a nuestro Wazuh Manager. Este actúa como un cortafuegos virtual a nivel de red, aplicando de forma predeterminada políticas restrictivas de denegación y controlando de manera minuciosa el tráfico de entrada (*inbound*) y salida (*outbound*) de la instancia.

### **Configuración de puertos**

* **Puerto 22 (TCP):** Para conectarte por SSH.  
* **Puerto 443 (TCP):** Para acceder al panel web (Dashboard) de Wazuh.  
* **Puerto 1514 (TCP):** Para que los agentes envíen sus logs al servidor.  
* **Puerto 1515 (TCP):** Para registrar nuevos agentes.  
* **Puerto 55000 (TCP):** Para la API de Wazuh.  
* Y las reglas de salida es por **defaut 0.0.0.0**  
  **![][image2]**


## **3\. Conexión Remota vía SSH**

El acceso para la administración interna de la máquina virtual se realiza desde el terminal local mediante el protocolo cifrado **SSH**, utilizando autenticación robusta basada en la llave privada asimétrica .pem generada en el momento de crear la instancia en AWS.

**Procedimiento técnico de conexión:**

_Asignar permisos UNIX estrictos para que el sistema acepte la clave privada_  
```bash  
chmod 400 claves-cyberarena.pem  
````

_Conectar de manera segura al servidor utilizando el usuario administrador nativo_  

```bash  
ssh -i "claves-cyberarena.pem" ec2-user@3.229.242.100
```
