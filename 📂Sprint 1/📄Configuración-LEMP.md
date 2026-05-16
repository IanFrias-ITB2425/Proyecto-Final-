# 🚀 Despliegue y Hardening del Servidor Web (LEMP)

## 📊 1. Identificación del Activo
El servidor principal de aplicaciones corre sobre una instancia optimizada de Ubuntu para garantizar estabilidad y rendimiento.

| Atributo | Detalle Técnico |
| :--- | :--- |
| **ID de Instancia** | `i-0ff8ac455c7ff66e3` |
| **IP Pública** | `34.254.144.104` 🌐 |
| **Sistema Operativo** | Ubuntu 24.04 LTS 🐧 |
| **Nombre de Host** | `ip-172-31-46-211` |

---

## 🛠️ 2. Stack Tecnológico (LEMP)
Se ha configurado un entorno de alto rendimiento compuesto por:

1.  **Nginx:** Servidor web configurado para procesar peticiones rápidas.
2.  **MariaDB:** Base de datos relacional para el sistema `cyberarena`.
3.  **PHP 8.3:** Motor para la lógica de la aplicación.

### 🔐 Seguridad de Base de Datos
Se eliminaron los usuarios anónimos y las bases de datos de prueba mediante `mysql_secure_installation`, asignando el usuario administrador `arena_sys`.

---

## 🧱 3. Fortificación WAF (ModSecurity)
Para proteger el servidor contra ataques de **Capa 7** (Inyección SQL, Cross-Site Scripting), hemos implementado un Firewall de Aplicaciones Web.

### Configuración Crítica:
Cambiamos el motor de reglas de "Solo detección" a "Bloqueo Activo":

sudo sed -i 's/SecRuleEngine DetectionOnly/SecRuleEngine On/' /etc/modsecurity/modsecurity.con

