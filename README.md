🛠 Prerrequisitos
Antes de comenzar, asegúrate de tener instaladas las siguientes tecnologías:

Software Requerido
PHP 8.4.0 (Thread Safe)

Composer 2.8.12

Laravel Installer 5.18.0

Docker Desktop 4.47.0 (Engine 28.4.0)

Git 2.51.0 o superior

Microsoft Visual C++ Redistributable (x64)

Enlaces de Descarga
📥 PHP: https://www.php.net/downloads.php

📥 Composer: https://getcomposer.org/

📥 Docker: https://www.docker.com/get-started/

📥 VC++ Redistributable: https://learn.microsoft.com/en-us/cpp/windows/latest-supported-vc-redist

⚙️ Configuración del Entorno
Configuración de PHP
Después de instalar PHP, agrega la ruta de PHP a las variables de entorno del sistema y habilita las siguientes extensiones en el archivo php.ini:

ini
extension_dir = "ext"
extension=pdo_pgsql
extension=pgsql
extension=mbstring
extension=openssl
extension=curl
extension=fileinfo
Instalación de Laravel Installer
bash
composer global require laravel/installer
🚀 Levantar el Proyecto
Sigue estos pasos para configurar y ejecutar el proyecto:

1. Clonar el Repositorio
bash
git clone https://github.com/rcalexis/Sistema_Almacen-Backend.git
cd Sistema_Almacen-Backend
2. Configurar Variables de Entorno
bash
cp .env.example .env
Edita el archivo .env y configura las credenciales de la base de datos:

env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=sistema_almacen
DB_USERNAME=almacen_user
DB_PASSWORD=Sistem2025
3. Configuración de Laravel
bash
# Generar clave de aplicación
php artisan key:generate

# Instalar dependencias
composer install

# Instalar Laravel Sanctum
php artisan install:api
4. Levantar Contenedores Docker
bash
docker-compose up -d
5. Configurar Base de Datos en TablePlus
Crea una nueva conexión PostgreSQL con los siguientes parámetros:

Name: AlmacenDB

Host: 127.0.0.1

Port: 5432

User: almacen_user

Password: Almacen2025

Database: sistema_almacen

Nota: Haz clic en "Test" y si aparece "Connection successful", guarda la configuración.

6. Migrar Base de Datos
bash
php artisan migrate
📋 Verificación
Una vez completados todos los pasos, el proyecto debería estar ejecutándose correctamente. Verifica que todos los servicios estén funcionando y que puedas acceder a la aplicación.

🆘 Soporte
Si encuentras algún problema durante la instalación, verifica que:

Todas las tecnologías estén correctamente instaladas

Las extensiones de PHP estén habilitadas

Docker esté ejecutándose

Las credenciales de la base de datos sean correctas