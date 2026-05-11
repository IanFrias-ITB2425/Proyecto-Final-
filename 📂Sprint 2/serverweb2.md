# Documentación Técnica Detallada: Paso a Paso del Proyecto

Este documento describe cada fase del despliegue de seguridad basándose en las evidencias gráficas obtenidas durante el proceso de Hardening y Monitoreo.

---

### **Fase 1: Preparación del WAF (Web Application Firewall)**

#### **Imagen 17: Instalación de Dependencias**
Se realiza la actualización de los repositorios del sistema y la instalación del módulo `libapache2-mod-security2` para dotar al servidor de capacidades de filtrado de tráfico.
![Reglas de Entrada](../imagenes%20rehan/17.png)

#### **Imagen 18: Activación del SecRuleEngine**
Configuración del archivo `modsecurity.conf`. Se cambia el valor de `DetectionOnly` a `On`. Este paso es crítico para que el firewall bloquee activamente las amenazas detectadas en lugar de solo generar alertas.
![Reglas de Entrada](../imagenes%20rehan/18.png)

#### **Imagen 19: Implementación de OWASP CRS**
Descarga y despliegue del **Core Rule Set** de OWASP. Este conjunto de reglas proporciona la lógica necesaria para identificar ataques como Inyecciones SQL, XSS y escaneos de vulnerabilidades.
![Reglas de Entrada](../imagenes%20rehan/19.png)

---

### **Fase 2: Validación y Orquestación**

#### **Imagen 20: Test de Penetración (SQLi)**
Prueba de concepto mediante `curl`. Se intenta una inyección SQL simple. El servidor responde con un **403 Forbidden**, confirmando que la regla de bloqueo funciona. El acceso a la raíz (`/`) sigue devolviendo un **200 OK**.
![Reglas de Entrada](../imagenes%20rehan/20.png)

#### **Imagen 21: Verificación de Sintaxis**
Uso del comando de prueba del servidor web para asegurar que la integración del módulo ModSecurity no contiene errores estructurales que puedan degradar el servicio.
![Reglas de Entrada](../imagenes%20rehan/21.png)

#### **Imagen 22: Configuración de Rutas y Exclusiones**
Edición del archivo de configuración para incluir los directorios de reglas activas y el archivo de **whitelist** (lista blanca) para minimizar falsos positivos en la aplicación.
![Reglas de Entrada](../imagenes%20rehan/22.png)

---

### **Fase 3: Auditoría y Conectividad SOC**

#### **Imagen 23: Análisis de Logs Forenses**
Inspección del log de errores de Nginx/Apache. Se observa la intercepción de ataques con detalles sobre la IP origen y el ID de la regla OWASP activada (ID 942100).
![Reglas de Entrada](../imagenes%20rehan/23.png)

#### **Imagen 24: Registro del Agente Wazuh**
Configuración del agente de seguridad para comunicarse con el servidor central (Manager) en AWS. Se establece la dirección IP `10.0.1.96` como destino de la telemetría.
![Reglas de Entrada](../imagenes%20rehan/24.png)

#### **Imagen 25: Auditoría Inicial con Lynis**
Ejecución de la herramienta Lynis para evaluar el estado de seguridad. Se obtiene un **Hardening Index de 66**, identificando puntos débiles en el kernel y servicios expuestos.
![Reglas de Entrada](../imagenes%20rehan/25.png)

#### **Imagen 26: Revisión de Sugerencias de Seguridad**
Análisis detallado de los hallazgos de Lynis, específicamente en las categorías de Firewalls y protección contra Malware.
![Reglas de Entrada](../imagenes%20rehan/26.png)

---

### **Fase 4: Monitoreo Avanzado e Integridad**

#### **Imagen 27: Configuración de FIM (Syscheck)**
Edición del archivo `ossec.conf` para activar el **File Integrity Monitoring**. Se definen los directorios críticos (`/etc`, `/var/www/html`) para ser escaneados cada 12 horas.
![Reglas de Entrada](../imagenes%20rehan/27.png)

#### **Imagen 28: Parámetros de Notificación**
Configuración del nivel de alertas que deben ser enviadas al dashboard centralizado para su análisis por el equipo de seguridad.
![Reglas de Entrada](../imagenes%20rehan/28.png)

#### **Imagen 29: Sincronización del Servicio**
Reinicio del servicio `wazuh-agent` para cargar todas las nuevas configuraciones de integridad y red.
![Reglas de Entrada](../imagenes%20rehan/29.png)

---

### **Fase 5: Visualización en el Dashboard SOC**

#### **Imagen 30: Acceso al Manager**
Inicio de sesión en la interfaz web de Wazuh, mostrando el resumen de seguridad y el estado de salud de la plataforma.
![Reglas de Entrada](../imagenes%20rehan/30.png)

#### **Imagen 31: Inventario de Agentes Conectados**
Vista detallada de los nodos activos. Se confirma que el servidor Ubuntu está sincronizado y enviando logs correctamente.
![Reglas de Entrada](../imagenes%20rehan/31.png)

#### **Imagen 32: Eventos de Seguridad en Tiempo Real**
Cronología de alertas detectadas, clasificadas por niveles de gravedad de acuerdo con el estándar de Wazuh.
![Reglas de Entrada](../imagenes%20rehan/32.png)

#### **Imagen 33: Geolocalización de Amenazas**
Mapa interactivo que muestra la ubicación geográfica de las direcciones IP que han intentado atacar el servidor web.
![Reglas de Entrada](../imagenes%20rehan/33.png)

#### **Imagen 34: Dashboard de Eventos WAF**
Gráficos específicos que desglosan los ataques detectados por ModSecurity, facilitando la identificación de vectores como SQLi y XSS.
![Reglas de Entrada](../imagenes%20rehan/34.png)

#### **Imagen 35: Histórico de Integridad de Archivos**
Panel final que muestra las modificaciones detectadas en el sistema de archivos, asegurando el control total sobre cualquier cambio no autorizado.
![Reglas de Entrada](../imagenes%20rehan/35.png)

---
**Documentación completa: Estructura de Proyecto y Evidencias Visuales.**
