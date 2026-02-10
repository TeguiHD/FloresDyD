# 🌸 Flores D&D - Guía de Deploy en Hosting Compartido (cPanel)

> **Stack:** PHP 8.2+ | Laravel 12 | Livewire 3 | MariaDB | Tailwind CSS 4  
> **Requisito del hosting:** PHP 8.2+, MariaDB/MySQL 8.0+, mod_rewrite habilitado  
> **Sin acceso SSH** — Todo se hace vía cPanel, File Manager, phpMyAdmin y scripts PHP.

---

## 📋 Índice

1. [Preparación Local (Build)](#1-preparación-local-build)
2. [Estructura en el Hosting](#2-estructura-en-el-hosting)
3. [Subir Archivos al Hosting](#3-subir-archivos-al-hosting)
4. [Configurar .env de Producción](#4-configurar-env-de-producción)
5. [Configurar Base de Datos](#5-configurar-base-de-datos)
6. [Setup Inicial en Hosting](#6-setup-inicial-en-hosting)
7. [Configurar Cron Job](#7-configurar-cron-job)
8. [Verificación Final](#8-verificación-final)
9. [Mantenimiento y Actualizaciones](#9-mantenimiento-y-actualizaciones)
10. [Solución de Problemas](#10-solución-de-problemas)

---

## 1. Preparación Local (Build)

### Opción A: Script Automático
```bash
cd flores-dyd
chmod +x deploy-tools/build-for-deploy.sh
./deploy-tools/build-for-deploy.sh
```

### Opción B: Manual paso a paso
```bash
cd flores-dyd

# 1. Instalar dependencias PHP (solo producción)
composer install --no-dev --optimize-autoloader

# 2. Compilar assets (Vite + Tailwind)
npm ci
npm run build

# 3. Cachear config de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4. Exportar base de datos (o hacerlo desde phpMyAdmin local)
mysqldump -u root flores_dyd > deploy-tools/database-export.sql
```

### ¿Qué genera el build?
- `public/build/` → CSS y JS compilados y minificados con hash (Vite)
- `bootstrap/cache/` → Config, rutas y vistas cacheadas
- `vendor/` → Dependencias PHP (sin dev)

> ⚠️ **IMPORTANTE:** El build se hace EN LOCAL. Una vez compilado, el proyecto es PHP puro — no necesita Node.js ni npm en el servidor.

---

## 2. Estructura en el Hosting

### Estructura RECOMENDADA (Opción A) ⭐

La mayoría de hostings cPanel permiten acceder a directorios fuera de `public_html`.  
**Esta es la opción más segura** porque el código PHP queda inaccesible desde la web:

```
/home/usuario/
├── floresdyd/                    ← Proyecto Laravel (FUERA de public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   ├── artisan
│   ├── composer.json
│   └── cron.php                  ← Para cron jobs
│
└── public_html/                  ← Document Root (contenido de public/)
    ├── .htaccess
    ├── index.php                 ← Modificado para apuntar a ../floresdyd
    ├── build/
    │   ├── assets/
    │   └── manifest.json
    ├── fonts/
    ├── images/
    ├── favicon.ico
    ├── robots.txt
    └── storage → ../floresdyd/storage/app/public  (symlink)
```

**Para esto, modifica `public_html/index.php`:**
```php
<?php
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Apuntar al directorio del proyecto Laravel (fuera de public_html)
if (file_exists($maintenance = __DIR__.'/../floresdyd/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../floresdyd/vendor/autoload.php';

(require_once __DIR__.'/../floresdyd/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

Copia también el `.htaccess` de `public/` a `public_html/`.

### Estructura ALTERNATIVA (Opción B)

Si todo debe ir dentro de `public_html`:

```
/home/usuario/public_html/        ← Todo aquí
├── .htaccess                     ← Redirige a public/ (usa .htaccess-root)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── cron.php
└── public/
    ├── .htaccess
    ├── index.php
    ├── build/
    ├── fonts/
    ├── images/
    └── storage → ../storage/app/public
```

Copia `deploy-tools/.htaccess-root` como `.htaccess` en la raíz de `public_html/`.

> ⚠️ La Opción B expone archivos del proyecto al web. El `.htaccess-root` bloquea acceso a directorios sensibles (.env, vendor, etc.), pero la **Opción A es siempre más segura**.

---

## 3. Subir Archivos al Hosting

### Vía cPanel File Manager (recomendado)
1. En local, comprimir el proyecto en `.zip` (excluir `node_modules/`, `.git/`, `tests/`)
2. cPanel → **Administrador de Archivos**
3. Navegar al directorio destino
4. Click **Cargar** → subir el `.zip`
5. Click derecho sobre el `.zip` → **Extract**
6. Eliminar el `.zip` después de extraer

### Vía FTP (FileZilla)
1. cPanel → **Cuentas FTP** → obtener credenciales
2. Conectar con FileZilla
3. Subir todos los archivos al directorio correcto

### ❌ NO subir estos archivos/carpetas:
| No subir | Razón |
|----------|-------|
| `node_modules/` | Solo necesario para compilar, ya se hizo localmente |
| `.git/` | Control de versiones, innecesario en producción |
| `tests/` | Tests, no se usan en producción |
| `deploy-tools/` | Solo subir los scripts individuales que necesites |
| `deploy-ready/` | Carpeta temporal del build script |
| `.env` local | Crear uno nuevo con datos del hosting |

---

## 4. Configurar .env de Producción

Crear archivo `.env` en la raíz del proyecto en el hosting (vía File Manager → Nuevo archivo):

```dotenv
APP_NAME="Flores D&D"
APP_ENV=production
APP_KEY=base64:COPIAR_DE_TU_ENV_LOCAL
APP_DEBUG=false
APP_TIMEZONE=America/Santiago
APP_URL=https://tudominio.cl

APP_LOCALE=es
APP_FALLBACK_LOCALE=es

BCRYPT_ROUNDS=12

LOG_CHANNEL=daily
LOG_STACK=daily
LOG_LEVEL=error

# ── BASE DE DATOS (cPanel → Bases de datos MySQL) ──
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=usuario_floresdyd
DB_USERNAME=usuario_floresdyd
DB_PASSWORD=contraseña_segura

# ── SEGURIDAD ──
PASSWORD_PEPPER=GENERAR_PEPPER_UNICO
DB_ENCRYPTION_KEY=GENERAR_KEY_UNICA

# ── CORREO (Gmail con App Password) ──
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="contacto@tudominio.cl"
MAIL_FROM_NAME="${APP_NAME}"

# ── COLA DE EMAILS ──
# Con cron job configurado:
QUEUE_CONNECTION=database
# Sin cron job (emails se envían al instante, más lento):
# QUEUE_CONNECTION=sync

# ── SESIONES Y CACHE ──
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_DOMAIN=.tudominio.cl

CACHE_STORE=database
CACHE_PREFIX=floresdyd_

FILESYSTEM_DISK=local

# ── RATE LIMITING ──
RATE_LIMIT_LOGIN_ATTEMPTS=5
RATE_LIMIT_LOGIN_DECAY_MINUTES=15

# ── NEGOCIO ──
FLORES_ADMIN_EMAIL=admin@tudominio.cl
FLORES_STOCK_RESERVATION_MINUTES=45
FLORES_FRAUD_SCORE_THRESHOLD=40
FLORES_CURRENCY=CLP
FLORES_TIMEZONE=America/Santiago

# ── OCR DESHABILITADO EN HOSTING COMPARTIDO ──
OCR_ENABLED=false
```

### ⚠️ Notas clave del .env:
- **`APP_KEY`**: Copiar la misma de tu `.env` local (es `base64:...`)
- **`APP_DEBUG=false`**: SIEMPRE false en producción
- **`DB_HOST=localhost`**: En hosting compartido, casi siempre es `localhost`
- **`DB_DATABASE`**: En cPanel, las BD llevan prefijo del usuario: `usuario_nombrebd`
- **`QUEUE_CONNECTION`**: `database` + cron (ideal) o `sync` (alternativa simple)
- **`SESSION_DOMAIN`**: Con punto inicial para incluir subdominios (`.tudominio.cl`)
- **`OCR_ENABLED=false`**: Hosting compartido no tiene Tesseract

---

## 5. Configurar Base de Datos

### Crear BD en cPanel:
1. cPanel → **Bases de datos MySQL**
2. Crear nueva base de datos → nombre (ej: `floresdyd`)  
   *Resultará en: `usuario_floresdyd`*
3. Crear nuevo usuario con contraseña fuerte
4. **Asignar TODOS los privilegios** al usuario sobre la BD

### Importar datos:

#### Opción A: Importar SQL exportado (RECOMENDADA)
1. Exportar BD local: phpMyAdmin local o `mysqldump`
2. cPanel → **phpMyAdmin** → Seleccionar la BD
3. Pestaña **Importar** → Subir `.sql` → **Continuar**

#### Opción B: Migrar desde script de setup
1. Subir proyecto al hosting + configurar `.env`
2. Subir `deploy-tools/setup-database.php` a `public_html/`
3. Acceder a:
   ```
   https://tudominio.cl/setup-database.php?key=TU_CLAVE&action=migrate
   ```
4. Si necesitas datos iniciales (seeders):
   ```
   https://tudominio.cl/setup-database.php?key=TU_CLAVE&action=seed
   ```

---

## 6. Setup Inicial en Hosting

Una vez subidos los archivos, BD configurada y `.env` creado:

### Paso 1: Verificar estado
```
https://tudominio.cl/setup-database.php?key=TU_CLAVE&action=status
```
Verificar:
- ✅ PHP 8.2+ instalado
- ✅ Extensiones: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd
- ✅ Conexión a BD exitosa
- ✅ Directorios con permisos de escritura

### Paso 2: Crear symlink de storage
```
https://tudominio.cl/setup-symlink.php?key=TU_CLAVE
```

### Paso 3: Optimizar para producción
```
https://tudominio.cl/setup-database.php?key=TU_CLAVE&action=optimize
```
Esto cachea configuración, rutas y vistas (mejora rendimiento significativamente).

### Paso 4: 🔴 ELIMINAR scripts de setup
Desde cPanel File Manager, **ELIMINAR inmediatamente**:
- `setup-symlink.php`
- `setup-database.php`

> Estos scripts dan acceso total a tu aplicación. **No dejarlos en producción NUNCA.**

---

## 7. Configurar Cron Job

### ¿Por qué es necesario?
Los 9 Mailables del proyecto (`WelcomeMail`, `OrderCreatedMail`, `PaymentVerifiedMail`, etc.) implementan `ShouldQueue`, lo que significa que los emails se encolan en la tabla `jobs` de la BD en lugar de enviarse al instante. El cron job procesa esa cola.

También ejecuta:
- Limpieza de reservas de stock expiradas (cada 5 min)
- Limpieza de tokens expirados (diario)
- Poda de logs de auditoría (mensual)

### Configurar en cPanel:
1. cPanel → **Trabajos de Cron** (Cron Jobs)
2. Configurar:
   - **Frecuencia:** Cada 5 minutos → `*/5 * * * *`
   - **Comando:**
     ```
     /usr/local/bin/php /home/USUARIO/floresdyd/cron.php >> /dev/null 2>&1
     ```
   
   > Reemplaza `USUARIO` con tu nombre de usuario de cPanel y ajusta la ruta al proyecto.  
   > Para encontrar la ruta de PHP: probar `/usr/bin/php`, `/usr/local/bin/php`, o `/opt/cpanel/ea-php82/root/usr/bin/php`

### Si NO puedes configurar Cron Jobs:
Cambiar en `.env`:
```dotenv
QUEUE_CONNECTION=sync
```
Los emails se enviarán de inmediato en el request (la página tardará un poco más al crear pedidos, pero **todo funcionará correctamente**).

---

## 8. Verificación Final

### Checklist post-deploy:

- [ ] `https://tudominio.cl` → Página de inicio carga correctamente
- [ ] CSS y JS cargan sin errores (revisar consola del navegador con F12)
- [ ] Imágenes de productos se ven (symlink de storage funciona)
- [ ] Registro de usuario funciona
- [ ] Login funciona correctamente
- [ ] Panel admin accesible en `/admin`
- [ ] Crear pedido de prueba → email de confirmación llega
- [ ] Formulario de contacto funciona
- [ ] HTTPS funciona con certificado SSL
- [ ] `.env`, `vendor/`, `app/` NO son accesibles desde el navegador

### Activar HTTPS:
1. cPanel → **SSL/TLS Status** o **Let's Encrypt™ SSL**
2. Activar certificado para tu dominio
3. El `.htaccess` ya incluye headers de seguridad

---

## 9. Mantenimiento y Actualizaciones

### Para actualizar el sitio:

1. **En local:** Hacer cambios → `npm run build` → `composer install --no-dev`
2. **Subir al hosting:** Solo archivos modificados
3. **Limpiar cache:**
   - Subir temporalmente `setup-database.php` a `public_html/`
   - `?action=clear-cache` → luego `?action=optimize`
   - **ELIMINAR** `setup-database.php` de inmediato

### Archivos a re-subir según el cambio:
| Si cambiaste... | Re-subir... |
|-----------------|-------------|
| CSS, JS, vistas Blade | `public/build/` completo |
| Lógica PHP | `app/` |
| Plantillas Blade | `resources/views/` |
| Rutas | `routes/` + limpiar cache |
| Configuración | `config/` + limpiar cache |
| Migraciones nuevas | `database/migrations/` + ejecutar migrate |

### Backup periódico:
- cPanel → **Asistente de Copia de Seguridad**
- phpMyAdmin → Exportar BD regularmente

---

## 10. Solución de Problemas

### ❌ Error 500 al acceder al sitio
1. Verificar que `.env` existe y tiene `APP_KEY`
2. Permisos: `storage/` y `bootstrap/cache/` → **755** (recursivo)
3. Revisar `storage/logs/laravel.log` desde File Manager
4. Verificar PHP 8.2+ en cPanel → **MultiPHP Manager**

### ❌ CSS/JS no cargan (página sin estilos)
1. Verificar que `public/build/` contiene `manifest.json` y `assets/`
2. Verificar que `npm run build` se ejecutó localmente antes de subir
3. Consola del navegador (F12) → buscar errores 404 en assets

### ❌ Imágenes de productos no se ven
1. Verificar symlink: `public/storage` apunta correctamente
2. Si symlink no funciona: copiar manualmente contenido de `storage/app/public/` dentro de `public/storage/`
3. Permisos de `storage/app/public/` → **755**

### ❌ Emails no llegan
1. Verificar credenciales SMTP en `.env`
2. Si `QUEUE_CONNECTION=database`: verificar cron job (revisar `storage/logs/cron.log`)
3. Solución rápida: `QUEUE_CONNECTION=sync` en `.env`
4. Algunos hostings bloquean puerto 587 → probar puerto 465 con `MAIL_ENCRYPTION=ssl`

### ❌ "CSRF token mismatch"
1. Verificar `SESSION_DOMAIN` en `.env` (debe coincidir con tu dominio)
2. Permisos de `storage/framework/sessions/` → escritura (755)
3. Si `SESSION_DRIVER=database`: verificar que tabla `sessions` existe

### ❌ Permisos de archivos
En cPanel File Manager → seleccionar carpeta/archivo → **Change Permissions**:
```
storage/          → 755 (recursivo)
bootstrap/cache/  → 755
.env              → 644
```

---

## 📁 Herramientas de Deploy (deploy-tools/)

| Archivo | Dónde va en hosting | Para qué sirve | ¿Permanente? |
|---------|--------------------|-----------------|----|
| `setup-symlink.php` | `public_html/` | Crear symlink de storage | ❌ Eliminar después |
| `setup-database.php` | `public_html/` | Migrar, cachear, diagnosticar | ❌ Eliminar después |
| `cron.php` | Raíz del proyecto | Queue worker + scheduler | ✅ Permanente |
| `.htaccess-root` | Raíz (solo Opción B) | Redirigir a public/ | ✅ Si aplica |
| `build-for-deploy.sh` | Solo local | Automatizar build | — No subir |

> ⚠️ **SEGURIDAD:** Los archivos `setup-*.php` deben **ELIMINARSE** del hosting inmediatamente después del setup inicial. Dejarlos expone tu aplicación completamente.
