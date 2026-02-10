#!/bin/bash
# ============================================================
# FLORES D&D - Script de Build Local para Deploy en cPanel
# ============================================================
# 
# Ejecutar ANTES de subir al hosting:
#   chmod +x deploy-tools/build-for-deploy.sh
#   ./deploy-tools/build-for-deploy.sh
#
# Esto prepara todo para subir vía cPanel File Manager o FTP.
# ============================================================

set -e

echo "🌸 Flores D&D - Build para Deploy en Hosting Compartido"
echo "========================================================="
echo ""

PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$PROJECT_DIR"

echo "📁 Directorio del proyecto: $PROJECT_DIR"
echo ""

# 1. Instalar dependencias de PHP (solo producción)
echo "📦 1/6 - Instalando dependencias de Composer (producción)..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "✅ Composer listo."
echo ""

# 2. Instalar dependencias de Node y compilar assets
echo "📦 2/6 - Instalando dependencias de Node..."
npm ci
echo "✅ Node modules instalados."
echo ""

echo "🔨 3/6 - Compilando assets con Vite (producción)..."
npm run build
echo "✅ Assets compilados en public/build/"
echo ""

# 3. Cachear configuración de Laravel
echo "⚡ 4/6 - Cacheando configuración de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✅ Configuración cacheada."
echo ""

# 4. Exportar base de datos
echo "💾 5/6 - Exportando base de datos..."
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)
DUMP_FILE="deploy-tools/database-export.sql"

if command -v mysqldump &> /dev/null && [ -n "$DB_NAME" ]; then
    if [ -n "$DB_PASS" ]; then
        mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$DUMP_FILE" 2>/dev/null && \
            echo "✅ Base de datos exportada a $DUMP_FILE" || \
            echo "⚠️  No se pudo exportar la BD. Exporta manualmente desde phpMyAdmin."
    else
        mysqldump -u "$DB_USER" "$DB_NAME" > "$DUMP_FILE" 2>/dev/null && \
            echo "✅ Base de datos exportada a $DUMP_FILE" || \
            echo "⚠️  No se pudo exportar la BD. Exporta manualmente desde phpMyAdmin."
    fi
else
    echo "⚠️  mysqldump no disponible o DB_DATABASE no configurado."
    echo "   Exporta manualmente desde phpMyAdmin o tu gestor de BD local."
fi
echo ""

# 5. Crear directorio de deploy
echo "📂 6/6 - Preparando estructura de deploy..."
DEPLOY_DIR="$PROJECT_DIR/deploy-ready"

# Limpiar deploy anterior si existe
rm -rf "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"

# Copiar todo excepto lo innecesario
rsync -av --progress "$PROJECT_DIR/" "$DEPLOY_DIR/" \
    --exclude='deploy-ready' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='.github' \
    --exclude='tests' \
    --exclude='.env' \
    --exclude='deploy-tools/database-export.sql' \
    --exclude='*.log' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='storage/debugbar' \
    2>/dev/null || {
        echo "⚠️  rsync no disponible, usando cp..."
        cp -r "$PROJECT_DIR"/* "$DEPLOY_DIR/" 2>/dev/null
        rm -rf "$DEPLOY_DIR/node_modules" "$DEPLOY_DIR/.git" "$DEPLOY_DIR/tests" "$DEPLOY_DIR/deploy-ready"
    }

# Asegurar que los directorios de storage existan (vacíos)
mkdir -p "$DEPLOY_DIR/storage/app/public"
mkdir -p "$DEPLOY_DIR/storage/framework/cache/data"
mkdir -p "$DEPLOY_DIR/storage/framework/sessions"
mkdir -p "$DEPLOY_DIR/storage/framework/views"
mkdir -p "$DEPLOY_DIR/storage/logs"
mkdir -p "$DEPLOY_DIR/bootstrap/cache"

# Crear archivos .gitkeep para que cPanel no ignore carpetas vacías
touch "$DEPLOY_DIR/storage/framework/cache/data/.gitkeep"
touch "$DEPLOY_DIR/storage/framework/sessions/.gitkeep"
touch "$DEPLOY_DIR/storage/framework/views/.gitkeep"
touch "$DEPLOY_DIR/storage/logs/.gitkeep"

# Copiar herramientas de deploy al public del deploy
cp "$PROJECT_DIR/deploy-tools/setup-symlink.php" "$DEPLOY_DIR/public/setup-symlink.php"
cp "$PROJECT_DIR/deploy-tools/setup-database.php" "$DEPLOY_DIR/public/setup-database.php"
cp "$PROJECT_DIR/deploy-tools/cron.php" "$DEPLOY_DIR/cron.php"

echo "✅ Estructura de deploy lista en: $DEPLOY_DIR"
echo ""

echo "========================================================="
echo "🎉 ¡BUILD COMPLETADO!"
echo "========================================================="
echo ""
echo "📋 PRÓXIMOS PASOS:"
echo ""
echo "1. Crear .env en el hosting con credenciales de producción"
echo "2. Subir contenido de deploy-ready/ al hosting via cPanel"
echo "3. Importar database-export.sql en phpMyAdmin del hosting"
echo "4. Acceder a https://tudominio.com/setup-symlink.php?key=TU_CLAVE"
echo "5. Acceder a https://tudominio.com/setup-database.php?key=TU_CLAVE"
echo "   - Verificar estado (?action=status)"
echo "   - Si migraste, ejecutar (?action=optimize)"
echo "6. Configurar Cron Job en cPanel"
echo "7. ELIMINAR setup-symlink.php y setup-database.php del hosting"
echo ""
echo "📖 Ver DEPLOY_CPANEL.md para la guía completa."
echo ""
