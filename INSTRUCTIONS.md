# Guía de Instalación - Territorios LSC

Sigue estos pasos para instalar el proyecto en una nueva computadora. Asegúrate de tener instalado **PHP (>= 8.1)**, **Composer**, **Node.js/NPM** y un servidor de base de datos como **MySQL** (puedes usar Laragon, XAMPP o Docker).

## 1. Clonar el repositorio
Abre una terminal y ejecuta:
```bash
git clone https://github.com/kevindayala/territoriosLSC.git
cd territoriosLSC
```

## 2. Instalar dependencias de PHP
```bash
composer install
```

## 3. Instalar dependencias de Frontend
```bash
npm install
npm run build
```

## 4. Configurar el archivo de entorno
Copia el archivo de ejemplo para crear tu configuración local:
* En Windows (PowerShell): `copy .env.example .env`
* En Linux/Mac o Git Bash: `cp .env.example .env`

**Importante:** Abre el archivo `.env` recién creado y configura los datos de tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_bd
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

## 5. Generar la clave de la aplicación
```bash
php artisan key:generate
```

## 6. Ejecutar migraciones y carga de datos
Crea la base de datos en tu servidor MySQL primero, luego ejecuta:
```bash
php artisan migrate --seed
```

## 7. Iniciar el servidor local
Si usas Laragon o XAMPP, simplemente accede a la URL que te proporciona el panel. Si quieres usar el servidor interno de Laravel:
```bash
php artisan serve
```

---

### Notas adicionales:
- **Base de Datos:** Los registros actuales (personas, territorios, etc.) no están en GitHub. Si necesitas los datos reales de la otra PC, debes exportar la base de datos (SQL Dump) e importarla en la nueva.
- **Vite:** Durante el desarrollo, si haces cambios en el CSS o JS, recuerda ejecutar `npm run dev`. Para producción usa `npm run build`.
