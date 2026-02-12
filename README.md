# Flores D&D - Floristería Semi E-commerce

Sistema de floristería profesional desarrollado con Laravel 12, Livewire 4, Flux UI y Tailwind CSS 4.

## 🌸 Características

- **Semi E-commerce**: Carrito de compras con checkout personalizado.
- **Seguridad OWASP 2025**: Argon2id + Peppering, AES-256-GCM para datos sensibles.
- **Responsive DVH**: Diseño adaptable a todas las pantallas.
- **Email Transaccional**: Notificaciones para todos los eventos.

## 🧱 Stack

- Laravel 12 + Livewire 4
- Tailwind CSS 4 + Vite
- MySQL / MariaDB

## ✅ Requisitos

- PHP 8.2+ con extensiones: sodium, intl, gd, mbstring, mysql, curl, xml, bcmath
- MariaDB 10.6+ o MySQL 8.0+
- Node.js 18+ y npm
- Composer 2.x

## 🚀 Instalación local (paso a paso)

1) Clonar y entrar al proyecto

```bash
cd /home/nicoholas/Documentos/Paginas/FloresDyD/flores-dyd
```

2) Instalar dependencias de PHP y Node

```bash
composer install
npm install
```

3) Configurar variables de entorno

```bash
cp .env.example .env
```

Edita `.env` y define valores reales para base de datos, mail y seguridad. Luego genera la clave de la app:

```bash
php artisan key:generate
```

4) Migrar y seeders

```bash
php artisan migrate
php artisan db:seed
```

Si necesitas un admin inicial, configura estas variables en `.env` antes de seed:

```
ADMIN_SEED_EMAIL=
ADMIN_SEED_PASSWORD=
ADMIN_SEED_NAME=
ADMIN_DEMO_EMAIL=
ADMIN_DEMO_PASSWORD=
ADMIN_DEMO_NAME=
```

5) Limpiar caches

```bash
php artisan optimize:clear
```

6) Levantar entorno de desarrollo

En una terminal:

```bash
npm run dev
```

En otra terminal:

```bash
php artisan serve
```

## 🧾 Comandos y funcionalidades

### Dependencias

```bash
composer install
```
Instala dependencias PHP (Laravel, Livewire, etc.).

```bash
npm install
```
Instala dependencias de frontend (Tailwind, Vite, etc.).

### Desarrollo

```bash
npm run dev
```
Compila assets en modo desarrollo con recarga en caliente.

```bash
php artisan serve
```
Levanta el servidor local de Laravel.

### Base de datos

```bash
php artisan migrate
```
Crea las tablas en la base de datos.

```bash
php artisan db:seed
```
Carga data inicial (roles, categorías, enlaces sociales y admin si está configurado).

```bash
php artisan db:seed --class=AdminUserSeeder
```
Ejecuta solo el seeder de administradores (usa variables `ADMIN_*`).

### Optimización y limpieza

```bash
php artisan optimize:clear
```
Limpia caches de configuración, rutas, vistas y app.

```bash
php artisan optimize
```
Genera cachés para producción.

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Cachea configuración, rutas y vistas.

### Producción (assets)

```bash
npm run build
```
Compila assets listos para producción.

## 🔒 Seguridad y secretos

- No subir `.env` al repositorio.
- No commitear `vendor/`, `node_modules/`, `public/build/`, `storage/` ni `public/storage/`.
- Las credenciales de admin se generan solo vía variables `ADMIN_*` (no hay cuentas hardcodeadas).

## ✅ Checklist antes de subir a GitHub

1) Verificar `.gitignore` (ya excluye secretos y builds).
2) Eliminar `node_modules/`, `vendor/`, `public/build/`, `storage/logs/` si existen localmente.
3) Revisar que `.env` no esté versionado.
4) Cambiar cualquier password o correo real en archivos de texto.

## 📧 Sistema de Emails

El sistema envía emails automáticos para:

- ✅ Bienvenida (nuevo cliente)
- ✅ Pedido creado
- ✅ Cambio de estado del pedido
- ✅ Pago verificado
- ✅ Pedido en camino
- ✅ Pedido entregado
- ✅ Reset de contraseña
- ✅ Contraseña cambiada
- ✅ Notificación a admin de nuevo pedido

## 🛡️ Seguridad

- **Passwords**: Argon2id (19 MiB, 2 iter) + HMAC-SHA256 Pepper
- **Datos sensibles**: AES-256-GCM via Laravel Crypt
- **CSRF**: Protección automática en formularios
- **XSS**: Sanitización automática de Blade
- **Rate Limiting**: 60 req/min por IP

## 📚 Documentacion

- `docs/ESPECIFICACION_SEGURIDAD_ARQUITECTURA.md`
- `docs/DEPLOY_CPANEL.md`

## 📄 Licencia

Desarrollado para Flores D&D. Todos los derechos reservados.
