<?php
/**
 * ============================================================
 * FLORES D&D - Crear Symlink de Storage
 * ============================================================
 * 
 * USO: Subir este archivo a public_html/ y acceder desde el navegador:
 *      https://tudominio.com/setup-symlink.php
 * 
 * IMPORTANTE: ¡ELIMINAR ESTE ARCHIVO DESPUÉS DE USARLO!
 * 
 * Este script reemplaza: php artisan storage:link
 * ============================================================
 */

// Clave de seguridad - CAMBIAR antes de subir
$SECRET_KEY = 'CAMBIAR_ESTA_CLAVE_SECRETA_2026';

if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    http_response_code(403);
    die('⛔ Acceso denegado. Uso: ?key=TU_CLAVE_SECRETA');
}

echo "<h2>🌸 Flores D&D - Setup Storage Symlink</h2><pre>";

$publicStoragePath = __DIR__ . '/storage';
$targetPath = __DIR__ . '/../storage/app/public';

// Verificar que el directorio destino existe
if (!is_dir($targetPath)) {
    echo "⚠️  Creando directorio storage/app/public...\n";
    mkdir($targetPath, 0755, true);
    echo "✅ Directorio creado.\n";
}

// Si ya existe el symlink o directorio, eliminarlo
if (is_link($publicStoragePath)) {
    echo "🔄 Eliminando symlink existente...\n";
    unlink($publicStoragePath);
} elseif (is_dir($publicStoragePath)) {
    echo "🔄 Eliminando directorio storage existente...\n";
    // Mover contenido si existe
    $files = glob($publicStoragePath . '/*');
    if (count($files) > 0) {
        echo "   📁 Moviendo " . count($files) . " archivos existentes al storage real...\n";
        foreach ($files as $file) {
            $basename = basename($file);
            $dest = $targetPath . '/' . $basename;
            if (!file_exists($dest)) {
                rename($file, $dest);
                echo "   → Movido: $basename\n";
            }
        }
    }
    rmdir($publicStoragePath);
}

// Crear el symlink con ruta relativa (más portable)
$result = symlink('../storage/app/public', $publicStoragePath);

if ($result && is_link($publicStoragePath)) {
    echo "\n✅ ¡Symlink creado exitosamente!\n";
    echo "   public/storage → ../storage/app/public\n";
    echo "\n🔒 AHORA ELIMINA ESTE ARCHIVO (setup-symlink.php) del servidor.\n";
} else {
    echo "\n❌ Error al crear symlink.\n";
    echo "   Alternativa: Crear manualmente desde cPanel File Manager.\n";
    echo "   O copiar físicamente los archivos de storage/app/public a public/storage/\n";
    
    // Plan B: Si symlink no funciona, crear directorio y archivo .htaccess de redirect
    echo "\n🔄 Intentando Plan B: Copiar archivos directamente...\n";
    if (!is_dir($publicStoragePath)) {
        mkdir($publicStoragePath, 0755, true);
    }
    
    // Crear un index.php que sirva archivos desde storage
    $indexContent = '<?php
// Proxy para archivos de storage cuando symlink no funciona
$requestedFile = ltrim($_SERVER["PATH_INFO"] ?? "", "/");
$storagePath = __DIR__ . "/../storage/app/public/" . $requestedFile;

if ($requestedFile && file_exists($storagePath) && is_file($storagePath)) {
    $mime = mime_content_type($storagePath);
    header("Content-Type: " . $mime);
    header("Content-Length: " . filesize($storagePath));
    header("Cache-Control: public, max-age=31536000");
    readfile($storagePath);
    exit;
}

http_response_code(404);
echo "Archivo no encontrado.";
';
    file_put_contents($publicStoragePath . '/index.php', $indexContent);
    echo "✅ Plan B aplicado: proxy de archivos creado en public/storage/index.php\n";
}

echo "\n</pre>";
echo "<p style='color:red;font-weight:bold'>⚠️ RECUERDA: Elimina este archivo después de usarlo.</p>";
