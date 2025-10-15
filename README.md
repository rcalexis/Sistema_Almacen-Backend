# 🛠 Sistema de Almacén – Backend

Este proyecto es un sistema de gestión de almacén desarrollado con Laravel y PostgreSQL. A continuación se detallan los pasos necesarios para instalar y ejecutar el entorno de desarrollo.

---

## 📋 Prerrequisitos

Antes de comenzar asegurate de tener instaladas las siguientes tecnologias:

### 🔧 Software Requerido

- **PHP 8.4.0 (Thread Safe)**
- **Composer 2.8.12**
- **Laravel Installer 5.18.0**
- **Docker Desktop 4.47.0 (Engine 28.4.0)**
- **Git 2.51.0 o superior**
- **Microsoft Visual C++ Redistributable (x64)**

### 📥 Enlaces de Descarga

- [PHP](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/)
- [Docker](https://www.docker.com/get-started/)
- [VC++ Redistributable](https://learn.microsoft.com/en-us/cpp/windows/latest-supported-vc-redist)

---

## ⚙️ Configuracion del Entorno

### 🔧 Configuracion de PHP

Después de instalar PHP:

1. Agrega la ruta de PHP a las variables de entorno del sistema.
2. Habilita las siguientes extensiones en el archivo `php.ini`:

```ini
extension_dir = "ext"
extension=pdo_pgsql
extension=pgsql
extension=mbstring
extension=openssl
extension=curl
extension=fileinfo

📦 Instalacion de Laravel Installer

composer global require laravel/installer

🚀 Levantar el Proyecto
1. Clonar el Repositorio
git clone https://github.com/rcalexis/Sistema_Almacen-Backend.git
cd Sistema_Almacen-Backend

2. Configurar Variables de Entorno
cp .env.example .env

Edita el archivo .env y configura las credenciales de la base de datos:
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=sistema_almacen
DB_USERNAME=almacen_user
DB_PASSWORD=Sistem2025

3. Configuración de Laravel
# Generar clave de aplicación
php artisan key:generate

# Instalar dependencias
composer install

# Instalar Laravel Sanctum
php artisan install:api


4. Levantar Contenedores Docker
docker-compose up -d


5. Configurar Base de Datos en TablePlus
Crea una nueva conexión PostgreSQL con los siguientes parámetros:
- Name: AlmacenDB
- Host: 127.0.0.1
- Port: 5432
- User: almacen_user
- Password: Sistem2025
- Database: sistema_almacen
Haz clic en "Test" y si aparece "Connection successful", guarda la configuracion.


6. Migrar Base de Datos
php artisan migrate

7. Levantar el Servidos local
php artisan serve







