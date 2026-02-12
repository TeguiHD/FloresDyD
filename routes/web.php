<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
use App\Livewire\Auth\Login as CustomerLogin;
use App\Livewire\Auth\Register as CustomerRegister;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Account\Dashboard as AccountDashboard;
use App\Livewire\Account\Orders as AccountOrders;
use App\Livewire\Account\OrderShow as AccountOrderShow;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Category;
use App\Models\Popup;
use App\Services\AuditService;

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

// =========================================
// AUTENTICACIÓN CLIENTES
// =========================================

Route::get('/login', CustomerLogin::class)->middleware('guest')->name('login');
Route::get('/registro', CustomerRegister::class)->middleware('guest')->name('register');
Route::get('/register', function () {
    return redirect()->route('register');
});
Route::get('/olvide-mi-contrasena', ForgotPassword::class)->middleware('guest')->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->middleware('guest')->name('password.reset');

// Google OAuth
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->middleware('guest')->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->middleware('guest')->name('auth.google.callback');

Route::post('/logout', function () {
    AuditService::logout();
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->middleware('auth')->name('logout');

// =========================================
// CHECKOUT (Sin autenticación requerida)
// =========================================

Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/checkout/exito/{order}', CheckoutSuccess::class)
    ->middleware('signed')
    ->name('checkout.success');

// Rastrear pedido
Route::get('/rastrear-pedido', TrackOrder::class)->name('track.order');
Route::get('/rastrear-pedido/{tracking_code}', TrackOrder::class)->name('track.order.code');

// =========================================
// CUENTA DEL CLIENTE
// =========================================

Route::middleware('auth')->prefix('mi-cuenta')->name('account.')->group(function () {
    Route::get('/', AccountDashboard::class)->name('dashboard');
    Route::get('/pedidos', AccountOrders::class)->name('orders');
    Route::get('/pedidos/{order}', AccountOrderShow::class)->name('orders.show');

    Route::get('/pedidos/{order}/comprobantes/{proof}', function (Order $order, PaymentProof $proof) {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($proof->order_id === $order->id, 404);

        $disk = Storage::disk('local');
        if (!$disk->exists($proof->file_path)) {
            abort(404);
        }

        return $disk->download($proof->file_path, $proof->original_filename);
    })->name('orders.proof');
});

// =========================================
// PÁGINAS LEGALES
// =========================================

Route::view('/aviso-de-privacidad', 'pages.politica-privacidad')->name('aviso-privacidad');
Route::view('/terminos-y-condiciones', 'pages.terminos-condiciones')->name('terminos');
Route::view('/politica-de-envios', 'pages.politica-envios')->name('politica-envios');
Route::view('/flores-a-domicilio-santiago', 'pages.flores-domicilio-santiago')->name('flores.santiago');
Route::view('/flores-a-domicilio-valdivia', 'pages.flores-domicilio-valdivia')->name('flores.valdivia');
Route::view('/sucursales', 'pages.sucursales')->name('sucursales');

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

    // Popups marketing: métricas
    Route::post('/popups/{popup}/view', function (Popup $popup) {
        $popup->increment('views_count');
        return response()->json(['ok' => true]);
    })->middleware('throttle:30,1')->name('api.popups.view');

    Route::post('/popups/{popup}/click', function (Popup $popup) {
        $popup->increment('clicks_count');
        return response()->json(['ok' => true]);
    })->middleware('throttle:30,1')->name('api.popups.click');
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
        route('flores.santiago'),
        route('flores.valdivia'),
        route('sucursales'),
    ];

    $categories = Category::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    foreach ($categories as $category) {
        $urls[] = route('coleccion.categoria', $category->slug);
    }

    $escape = fn($value) => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
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
        'Disallow: /registro',
        'Disallow: /register',
        'Disallow: /olvide-mi-contrasena',
        'Disallow: /reset-password',
        'Disallow: /mi-cuenta',
        'Disallow: /api',
        'Allow: /',
        "Sitemap: {$baseUrl}/sitemap.xml",
    ];

    return response(implode("\n", $lines) . "\n", 200)->header('Content-Type', 'text/plain');
});

// Admin routes
require __DIR__ . '/admin.php';

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
