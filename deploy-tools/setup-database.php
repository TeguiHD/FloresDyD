<?php
/**
 * ============================================================
 * FLORES D&D - Ejecutar Migraciones y Setup Inicial
 * ============================================================
 * 
 * USO: Subir este archivo a public_html/ y acceder desde el navegador:
 *      https://tudominio.com/setup-database.php?key=TU_CLAVE
 * 
 * IMPORTANTE: ¡ELIMINAR ESTE ARCHIVO DESPUÉS DE USARLO!
 * 
 * Este script reemplaza:
 *   - php artisan migrate
 *   - php artisan db:seed (opcional)
 *   - php artisan key:generate (si no hay key)
 * ============================================================
 */

// Clave de seguridad - CAMBIAR antes de subir
$SECRET_KEY = 'CAMBIAR_ESTA_CLAVE_SECRETA_2026';

if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    http_response_code(403);
    die('⛔ Acceso denegado. Uso: ?key=TU_CLAVE_SECRETA');
}

// Aumentar límites para migraciones grandes
set_time_limit(300);
ini_set('memory_limit', '256M');

echo "<h2>🌸 Flores D&D - Setup Database</h2><pre>";

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

$action = $_GET['action'] ?? 'status';

switch ($action) {
    case 'migrate':
        echo "🔄 Ejecutando migraciones...\n\n";
        Artisan::call('migrate', ['--force' => true]);
        echo Artisan::output();
        echo "\n✅ Migraciones completadas.\n";
        break;

    case 'migrate-fresh':
        echo "⚠️  ATENCIÓN: Esto eliminará TODAS las tablas y recreará la BD.\n";
        if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
            echo "🔄 Ejecutando migrate:fresh...\n\n";
            Artisan::call('migrate:fresh', ['--force' => true]);
            echo Artisan::output();
            echo "\n✅ Base de datos recreada.\n";
        } else {
            echo "Para confirmar, agrega &confirm=yes a la URL.\n";
        }
        break;

    case 'seed':
        echo "🌱 Ejecutando seeders...\n\n";
        Artisan::call('db:seed', ['--force' => true]);
        echo Artisan::output();
        echo "\n✅ Seeders completados.\n";
        break;

    case 'optimize':
        echo "⚡ Optimizando aplicación para producción...\n\n";
        
        echo "→ Cacheando configuración...\n";
        Artisan::call('config:cache');
        echo Artisan::output();
        
        echo "→ Cacheando rutas...\n";
        Artisan::call('route:cache');
        echo Artisan::output();
        
        echo "→ Cacheando vistas...\n";
        Artisan::call('view:cache');
        echo Artisan::output();

        echo "→ Cacheando eventos...\n";
        Artisan::call('event:cache');
        echo Artisan::output();
        
        echo "\n✅ Aplicación optimizada para producción.\n";
        break;

    case 'clear-cache':
        echo "🧹 Limpiando todos los caches...\n\n";
        
        Artisan::call('config:clear');
        echo Artisan::output();
        
        Artisan::call('route:clear');
        echo Artisan::output();
        
        Artisan::call('view:clear');
        echo Artisan::output();
        
        Artisan::call('cache:clear');
        echo Artisan::output();
        
        echo "\n✅ Cache limpiado.\n";
        break;

    case 'key-generate':
        echo "🔑 Generando APP_KEY...\n\n";
        Artisan::call('key:generate', ['--force' => true]);
        echo Artisan::output();
        echo "\n✅ APP_KEY generada.\n";
        break;

    case 'queue-work':
        echo "📧 Procesando cola de trabajos pendientes...\n\n";
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--max-time' => 55, // Máximo 55 segundos (límite de hosting)
        ]);
        echo Artisan::output();
        echo "\n✅ Cola procesada.\n";
        break;

    case 'storage-link':
        echo "🔗 Creando symlink de storage...\n\n";
        Artisan::call('storage:link');
        echo Artisan::output();
        echo "\n✅ Storage link creado.\n";
        break;

    case 'status':
    default:
        echo "📊 Estado de la aplicación:\n";
        echo "─────────────────────────────────\n";
        echo "PHP Version:    " . phpversion() . "\n";
        echo "Laravel:        " . app()->version() . "\n";
        echo "Environment:    " . app()->environment() . "\n";
        echo "Debug Mode:     " . (config('app.debug') ? 'ON ⚠️' : 'OFF ✅') . "\n";
        echo "APP_KEY:        " . (config('app.key') ? 'SET ✅' : 'NOT SET ❌') . "\n";
        echo "DB Connection:  " . config('database.default') . "\n";
        echo "Cache Driver:   " . config('cache.default') . "\n";
        echo "Queue Driver:   " . config('queue.default') . "\n";
        echo "Session Driver: " . config('session.driver') . "\n";
        echo "Mail Mailer:    " . config('mail.default') . "\n";
        echo "─────────────────────────────────\n";
        
        // Test DB connection
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            echo "DB Status:      CONECTADA ✅\n";
            
            // Contar tablas
            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            echo "Tablas en BD:   " . count($tables) . "\n";
        } catch (\Exception $e) {
            echo "DB Status:      ERROR ❌\n";
            echo "DB Error:       " . $e->getMessage() . "\n";
        }
        
        // Verificar extensiones PHP necesarias
        echo "\n📋 Extensiones PHP:\n";
        $required = ['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'gd'];
        foreach ($required as $ext) {
            $status = extension_loaded($ext) ? '✅' : '❌';
            echo "  $status $ext\n";
        }
        
        // Verificar permisos de escritura
        echo "\n📁 Permisos de escritura:\n";
        $dirs = [
            'storage/framework/cache' => __DIR__ . '/../storage/framework/cache',
            'storage/framework/sessions' => __DIR__ . '/../storage/framework/sessions',
            'storage/framework/views' => __DIR__ . '/../storage/framework/views',
            'storage/logs' => __DIR__ . '/../storage/logs',
            'storage/app/public' => __DIR__ . '/../storage/app/public',
            'bootstrap/cache' => __DIR__ . '/../bootstrap/cache',
        ];
        foreach ($dirs as $name => $path) {
            if (!is_dir($path)) {
                echo "  ⚠️  $name (no existe)\n";
            } elseif (is_writable($path)) {
                echo "  ✅ $name\n";
            } else {
                echo "  ❌ $name (sin permisos de escritura)\n";
            }
        }

        echo "\n─────────────────────────────────\n";
        echo "🔧 Acciones disponibles:\n";
        echo "  ?action=migrate        → Ejecutar migraciones\n";
        echo "  ?action=migrate-fresh  → Recrear BD desde cero\n";
        echo "  ?action=seed           → Ejecutar seeders\n";
        echo "  ?action=optimize       → Cachear config/rutas/vistas\n";
        echo "  ?action=clear-cache    → Limpiar todos los caches\n";
        echo "  ?action=key-generate   → Generar APP_KEY\n";
        echo "  ?action=queue-work     → Procesar cola de emails\n";
        echo "  ?action=storage-link   → Crear symlink de storage\n";
        echo "\nTodas las acciones requieren &key=TU_CLAVE\n";
        break;
}

echo "\n</pre>";
echo "<p style='color:red;font-weight:bold'>⚠️ ELIMINA este archivo del servidor cuando termines el setup.</p>";
