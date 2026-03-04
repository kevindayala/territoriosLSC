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

## 8. Despliegue en Producción (Servidor Web)

A continuación, se detalla el proceso paso a paso para subir tu aplicación a un servidor de producción típico (como Hostinger, cPanel, u otro hosting compartido/VPS):

### Paso A: Preparar los archivos en tu computadora
Antes de subir nada a internet, debemos dejar nuestro código optimizado y listo:
1. En tu computadora (Laragon/Local), abre tu terminal dentro de la carpeta del proyecto.
2. Ejecuta el comando para compilar los estilos visuales (CSS/JS) para producción:
   ```bash
   npm run build
   ```
   *(Notarás que se crea una carpeta llamada `public/build` con los archivos finales minificados).*
3. Comprime toda la carpeta de tu proyecto en un archivo `.zip` (para poder subirla fácilmente).
   * **⚠️ LO MÁS IMPORTANTE:** NO incluyas las carpetas `vendor` ni `node_modules` dentro del `.zip`. Si las incluyes, el archivo pesará gigabytes y tardará horas en subir. Tampoco incluyas tu archivo `.env` local.
   * *Solo necesitamos subir tu código fuente, las carpetas pesadas las generará el servidor él mismo.*

### Paso B: Subir los archivos a tu Hosting
Para que tu aplicación sea completamente segura contra hackers, **el código fuente no debe estar a la vista del público**. Por defecto, todo lo que pones en `public_html` es visible en internet. Así que lo haremos a prueba de balas:

1. Ve al **Administrador de Archivos (File Manager)** de tu panel de hosting (cPanel, Hostinger, etc.).
2. Sitúate en la **carpeta principal** de tu servidor (es decir, ubícate un nivel ANTES de entrar a la carpeta `public_html`, por ejemplo en `/home/tu_usuario/`).
3. Allí mismo, **crea una nueva carpeta** llamada `app-territorios` (o el nombre que prefieras).
4. Entra a esa nueva carpeta, **sube tu archivo `.zip`** allí, y luego **descomprímelo**.
   *(Ahora todo tu código está en un lugar 100% seguro y oculto del público: `/home/tu_usuario/app-territorios`).*
5. ¡Pero la gente necesita ver tu página web! Para conectarlo sin mover archivos engorrosos, en tu panel de control busca la sección de **Dominios** o **Sitios Web**.
6. Busca una opción llamada **Directorio Raíz** (o Document Root) de tu dominio. Generalmente verás que está apuntando a `public_html`.
7. **Cámbialo** borrando `public_html` y escribiendo en su lugar: `/app-territorios/public` (la carpeta pública que extrajiste dentro de tu proyecto).
8. Guarda los cambios. 
*(¡Al hacer esto, tu host ahora sabe que tu sitio web arranca seguro desde la carpeta public de tu app, y ya no usarás el clásico public_html para nada!)*

### Paso C: Crear la Base de Datos en la web
Tu web necesita donde guardar los datos en internet:
1. En el panel de control de tu hosting, busca y haz clic en **Bases de Datos MySQL**.
2. Escribe un nombre y dale a **Crear Nueva Base de Datos**.
3. Baja un poco y crea un **Nuevo Usuario MySQL** con una contraseña muy segura.
4. Por último, **Añade ese Usuario a la Base de Datos** que creaste en el paso 2 y otórgale **TODOS LOS PRIVILEGIOS**.
5. ¡Anota bien el Nombre BD, Usuario de la BD y la Contraseña!

### Paso D: Conectar la web con la Base de Datos (Archivo .env)
1. En el Administrador de Archivos de tu servidor, entra a la carpeta donde descomprimiste todo.
2. Verás un archivo llamado `.env.example`. Cámbiale el nombre a simplemente: `.env`
3. Dale a **Editar** a ese archivo y cambia las credenciales para que queden así:
   ```env
   # Modo Producción
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://tudominio.com # (Pon la URL real de tu página aquí)

   # Configuración de tu DB que anotaste en el Paso C
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nombre_de_la_bd_creada
   DB_USERNAME=usuario_creado
   DB_PASSWORD=contraseña_creada
   ```
4. Guarda los cambios.

### Paso E: Instalar las dependencias automáticas del servidor
Tu panel de hosting seguramente tiene una opción llamada **Terminal** o **Acceso SSH**. Ábrela:
1. Entra a la carpeta de tu proyecto desde esa terminal negra escribiendo algo como:
   `cd /home/tu_usuario/territoriosLSC` (o `cd public_html` si pusiste todo allí).
2. Dile al servidor que descargue los plugins de PHP (la carpeta `vendor` que no subimos):
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
3. Genera la llave de seguridad obligatoria de Laravel:
   ```bash
   php artisan key:generate
   ```
4. Arma las tablas de tu base de datos y crea los roles de usuario:
   ```bash
   php artisan migrate --force
   ```
5. **(TRUCO DE VELOCIDAD):** Ejecuta esto para que Laravel sea muchísimo más rápido recordando tus configuraciones de memoria:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Paso F: Permisos y Listo
Dependiendo de tu servidor, es recomendable asegurarse de que el sistema tenga permisos para escribir imágenes o guardar errores:
*   Asegúrate de que las carpetas `storage` y `bootstrap/cache` tengan permisos **775** o **755** (puedes cambiar esto dando clic derecho en ellas en el Administrador de Archivos -> Permisos).
*   ¡Eso es todo! Si configuras tu dominio para leer desde la carpeta `/public` del proyecto, tu página ya debería estar en línea y funcionando a la perfección.

---

**✨ Nota sobre Tareas Programadas (Crons):**  
A diferencia de aplicaciones Laravel tradicionales, **este proyecto NO requiere que configures tareas programadas (Crons) en tu servidor** para el cierre automático de territorios. Hemos implementado un sistema "Auto-Pilot" mediante un Middleware. Mientras el sistema reciba tráfico o se esté usando, él mismo revisará y completará de manera silenciosa las asignaciones que tengan más de 6 horas usando la caché para no impactar el rendimiento del servidor. No tienes que hacer nada más.

---

### Notas adicionales:
- **Base de Datos:** Los registros actuales (personas, territorios, etc.) no están en GitHub. Si necesitas los datos reales de la otra PC, debes exportar la base de datos (SQL Dump) e importarla en la nueva.
- **Vite:** Durante el desarrollo, si haces cambios en el CSS o JS, recuerda ejecutar `npm run dev`. Para producción usa `npm run build`.
