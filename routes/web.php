<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Coleccion;
use App\Livewire\Pages\ColeccionCategoria;
use App\Livewire\Pages\ProductoDetalle;
use App\Livewire\Pages\Ocasiones;
use App\Livewire\Pages\Servicios;
use App\Livewire\Pages\Nosotros;
use App\Livewire\Pages\Contacto;
use App\Livewire\Pages\Checkout;
use App\Livewire\Pages\CheckoutSuccess;
use App\Livewire\Pages\Search;
use App\Livewire\Pages\TrackOrder;
use App\Models\Category;

/*
|--------------------------------------------------------------------------
| Web Routes - Flores D&D
|--------------------------------------------------------------------------
|
| Rutas públicas del sitio de floristería.
| Todas las páginas usan componentes Livewire full-page.
|
*/

// =========================================
// PÁGINAS PÚBLICAS
// =========================================

// Home
Route::get('/', Home::class)->name('home');

// Colección de productos
Route::get('/coleccion', Coleccion::class)->name('coleccion');
Route::get('/coleccion/{category:slug}', ColeccionCategoria::class)->name('coleccion.categoria');

// Producto individual
Route::get('/producto/{product:slug}', ProductoDetalle::class)->name('producto');

// Ocasiones especiales
Route::get('/ocasiones', Ocasiones::class)->name('ocasiones');
Route::get('/ocasiones/{occasion}', Ocasiones::class)->name('ocasiones.detalle');

// Servicios
Route::get('/servicios', Servicios::class)->name('servicios');

// Nosotros
Route::get('/nosotros', Nosotros::class)->name('nosotros');

// Contacto
Route::get('/contacto', Contacto::class)->name('contacto');

// Búsqueda
Route::get('/buscar', Search::class)->name('search');

// Login (evitar error Fortify si no está configurado)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// =========================================
// CHECKOUT (Sin autenticación requerida)
// =========================================

Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/checkout/exito/{order}', CheckoutSuccess::class)->name('checkout.success');

// Rastrear pedido
Route::get('/rastrear-pedido', TrackOrder::class)->name('track.order');
Route::get('/rastrear-pedido/{tracking_code}', TrackOrder::class)->name('track.order.code');

// =========================================
// PÁGINAS LEGALES
// =========================================

Route::view('/aviso-de-privacidad', 'pages.politica-privacidad')->name('aviso-privacidad');
Route::view('/terminos-y-condiciones', 'pages.terminos-condiciones')->name('terminos');
Route::view('/politica-de-envios', 'pages.politica-envios')->name('politica-envios');
Route::view('/flores-a-domicilio-valdivia', 'pages.flores-domicilio-valdivia')->name('flores.valdivia');
Route::view('/flores-a-domicilio-santiago', 'pages.flores-domicilio-santiago')->name('flores.santiago');

// =========================================
// API ENDPOINTS PÚBLICOS (Para AJAX)
// =========================================

Route::prefix('api')->group(function () {
    // Verificar disponibilidad de entrega
    Route::post('/verificar-entrega', function () {
        // Lógica para verificar si se entrega en el código postal
        return response()->json(['available' => true]);
    })->name('api.verificar-entrega');
    
    // Calcular costo de envío
    Route::post('/calcular-envio', function () {
        // Lógica para calcular envío
        return response()->json(['shipping' => 0, 'free' => true]);
    })->name('api.calcular-envio');
});

// =========================================
// REDIRECCIONES SEO
// =========================================

// Redirecciones de URLs antiguas o alternativas
Route::redirect('/flores', '/coleccion', 301);
Route::redirect('/tienda', '/coleccion', 301);
Route::redirect('/catalogo', '/coleccion', 301);
Route::redirect('/about', '/nosotros', 301);
Route::redirect('/contact', '/contacto', 301);

// =========================================
// SEO: Sitemap & Robots
// =========================================

Route::get('/sitemap.xml', function () {
    $baseUrl = rtrim(config('app.url'), '/');
    $now = now()->toAtomString();

    $urls = [
        route('home'),
        route('coleccion'),
        route('ocasiones'),
        route('servicios'),
        route('nosotros'),
        route('contacto'),
        route('politica-envios'),
        route('aviso-privacidad'),
        route('terminos'),
        route('flores.valdivia'),
        route('flores.santiago'),
    ];

    $categories = Category::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    foreach ($categories as $category) {
        $urls[] = route('coleccion.categoria', $category->slug);
    }

    $escape = fn ($value) => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    $items = '';
    foreach (array_unique($urls) as $url) {
        $items .= "<url><loc>{$escape($url)}</loc><lastmod>{$now}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>";
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' .
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
        $items .
        '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/robots.txt', function () {
    if (!app()->environment('production')) {
        return response("User-agent: *\nDisallow: /\n", 200)->header('Content-Type', 'text/plain');
    }

    $baseUrl = rtrim(config('app.url'), '/');
    $lines = [
        'User-agent: *',
        'Disallow: /admin',
        'Disallow: /checkout',
        'Disallow: /login',
        'Disallow: /api',
        'Allow: /',
        "Sitemap: {$baseUrl}/sitemap.xml",
    ];

    return response(implode("\n", $lines) . "\n", 200)->header('Content-Type', 'text/plain');
});

// Admin routes
require __DIR__.'/admin.php';

// =========================================
// HONEYPOT TRAP ROUTES - Detección de bots
// =========================================
// Rutas que un usuario legítimo nunca visitaría.
// Los bots/scanners las buscan automáticamente.

Route::any('/wp-admin{path?}', function (Request $request) {
    \Illuminate\Support\Facades\Log::channel('security')->warning('Honeypot trap triggered', [
        'trap' => 'wp-admin',
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'url' => $request->fullUrl(),
    ]);
    abort(404);
})->where('path', '.*');

Route::any('/wp-login.php', function (Request $request) {
    \Illuminate\Support\Facades\Log::channel('security')->warning('Honeypot trap triggered', [
        'trap' => 'wp-login',
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    abort(404);
});

Route::any('/phpmyadmin{path?}', function (Request $request) {
    \Illuminate\Support\Facades\Log::channel('security')->warning('Honeypot trap triggered', [
        'trap' => 'phpmyadmin',
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    abort(404);
})->where('path', '.*');

Route::any('/xmlrpc.php', function (Request $request) {
    \Illuminate\Support\Facades\Log::channel('security')->warning('Honeypot trap triggered', [
        'trap' => 'xmlrpc',
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    abort(404);
});

Route::any('/administrator{path?}', function (Request $request) {
    \Illuminate\Support\Facades\Log::channel('security')->warning('Honeypot trap triggered', [
        'trap' => 'administrator',
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    abort(404);
})->where('path', '.*');
