# Documentación del Proyecto Final - Configuración de Servidor AWS

A continuación se detallan los pasos seguidos para la configuración de la instancia EC2, la base de datos y el despliegue del repositorio.

### 1. Resumen de la Instancia AWS
Se ha creado una instancia EC2 en AWS (t3.micro) con la dirección IP pública correspondiente para el despliegue del proyecto.
![Paso 0](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/0.png)

### 2. Configuración de Reglas de Seguridad
Se han configurado las reglas de entrada (Security Groups) para permitir el tráfico por los puertos 80 (HTTP), 22 (SSH) y 443 (HTTPS).
![Paso 1](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/1.png)

### 3. Conexión SSH mediante llave PEM
Uso de la terminal local para dar permisos a la llave `.pem` y conexión exitosa al servidor mediante SSH.
![Paso 2](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/2.png)

### 4. Instalación de Nginx
Proceso de instalación del servidor web Nginx en la instancia de Ubuntu.
![Paso 3](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/3.png)

### 5. Instalación de MariaDB
Instalación del sistema de gestión de bases de datos MariaDB Server.
![Paso 4](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/4.png)

### 6. Instalación de PHP y Módulos
Instalación de PHP y las librerías necesarias para la conexión con MySQL/MariaDB.
![Paso 5](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/5.png)

### 7. Securización de MariaDB
Ejecución del script `mysql_secure_installation` para configurar la contraseña de root y mejorar la seguridad de la BD.
![Paso 6](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/6.png)

### 8. Configuración de Base de Datos y Usuarios
Creación de la base de datos `arena_db`, creación del usuario `arena_sys` y asignación de privilegios.
![Paso 7](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/7.png)

### 9. Creación de Tablas
Definición de la tabla `logs_ataques` para el registro de incidentes dentro de la base de datos.
![Paso 8](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/8.png)

### 10. Clonación del Repositorio Final
Clonación del código fuente desde GitHub a la ruta `/var/www/html/` del servidor para su despliegue.
![Paso 9](https://github.com/IanFrias-ITB2425/Proyecto-Final-/raw/d712cdae7ab3317ef4716625de38e5086e04ab1f/imagenes%20rehan/9.png)
