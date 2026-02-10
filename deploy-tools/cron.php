<?php
/**
 * ============================================================
 * FLORES D&D - Cron Job Worker
 * ============================================================
 * 
 * Este archivo se ejecuta via Cron Job en cPanel.
 * Reemplaza: php artisan schedule:run
 * 
 * CONFIGURACIÓN EN CPANEL → Cron Jobs:
 *   Cada minuto:  * * * * *
 *   Comando:      php /home/USUARIO/public_html/../cron.php
 * 
 * O si el cron de cPanel necesita URL:
 *   wget -q -O /dev/null https://tudominio.com/cron.php?key=TU_CLAVE
 * 
 * NOTA: Este archivo se coloca EN LA RAÍZ del proyecto Laravel
 *       (NO en public_html), junto a artisan.
 * ============================================================
 */

// Clave de seguridad para acceso web (si se accede por URL)
$SECRET_KEY = 'CAMBIAR_ESTA_CLAVE_CRON_2026';

// Si se accede por web (no por CLI), verificar clave
if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
        http_response_code(403);
        die('Access denied');
    }
}

// Aumentar tiempo de ejecución
set_time_limit(120);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

$output = '';

// 1. Ejecutar el scheduler de Laravel
Artisan::call('schedule:run');
$output .= Artisan::output();

// 2. Procesar cola de trabajos pendientes (emails, etc.)
// --stop-when-empty: para cuando no hay más jobs
// --max-time=50: no exceder 50 segundos (seguridad)
Artisan::call('queue:work', [
    '--stop-when-empty' => true,
    '--max-time' => 50,
    '--max-jobs' => 10,
    '--tries' => 3,
]);
$output .= Artisan::output();

// Logging (solo en CLI, no en web)
if (php_sapi_name() === 'cli') {
    // Log silencioso a archivo
    $logFile = __DIR__ . '/storage/logs/cron.log';
    $logEntry = '[' . date('Y-m-d H:i:s') . '] ' . trim($output) . "\n";
    
    // Mantener log pequeño (máx 1MB)
    if (file_exists($logFile) && filesize($logFile) > 1048576) {
        // Truncar a las últimas 500 líneas
        $lines = file($logFile);
        $lines = array_slice($lines, -500);
        file_put_contents($logFile, implode('', $lines));
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
} else {
    // Acceso web: mostrar output
    echo "<pre>$output</pre>";
}
