<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Flores D&D Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración específica de la floristería
    |
    */

    // Información de contacto
    'phone' => env('FLORES_PHONE', '56900000000'),
    'phone_display' => env('FLORES_PHONE_DISPLAY', '+56 9 0000 0000'),
    'whatsapp' => env('FLORES_WHATSAPP', '56900000000'),
    'whatsapp_country_code' => env('FLORES_WHATSAPP_COUNTRY', '56'),
    'whatsapp_e164_strict' => env('FLORES_WHATSAPP_E164_STRICT', false),
    'email' => env('FLORES_EMAIL', 'contacto@floresdyd.com'),
    'address' => env('FLORES_ADDRESS', 'Valdivia y Santiago, Chile'),
    'map_embed_url' => env('FLORES_MAP_EMBED_URL'),

    // Sucursales
    'sucursales' => [
        [
            'nombre' => 'Sucursal Huechuraba',
            'direccion' => 'Av. Américo Vespucio Norte 359, Local 5',
            'comuna' => 'Huechuraba',
            'ciudad' => 'Santiago',
        ],
        [
            'nombre' => 'Sucursal Padre Hurtado',
            'direccion' => 'San Juan del Castillo 2539',
            'comuna' => 'Padre Hurtado',
            'ciudad' => 'Santiago',
        ],
    ],

    // Datos de pago
    'payment' => [
        'bank_transfer' => [
            'titular' => env('PAYMENT_BANK_TITULAR', ''),
            'rut' => env('PAYMENT_BANK_RUT', ''),
            'banco' => env('PAYMENT_BANK_BANCO', ''),
            'tipo_cuenta' => env('PAYMENT_BANK_TIPO', ''),
            'numero_cuenta' => env('PAYMENT_BANK_NUMERO', ''),
            'email' => env('PAYMENT_BANK_EMAIL', ''),
        ],
        'mercado_pago' => [
            'titular' => env('PAYMENT_MP_TITULAR', ''),
            'rut' => env('PAYMENT_MP_RUT', ''),
            'banco' => env('PAYMENT_MP_BANCO', 'Mercado Pago'),
            'tipo_cuenta' => env('PAYMENT_MP_TIPO', 'Cuenta Vista'),
            'numero_cuenta' => env('PAYMENT_MP_NUMERO', ''),
            'email' => env('PAYMENT_MP_EMAIL', ''),
        ],
    ],

    // Horarios
    'business_hours' => [
        'monday' => ['09:00', '19:00'],
        'tuesday' => ['09:00', '19:00'],
        'wednesday' => ['09:00', '19:00'],
        'thursday' => ['09:00', '19:00'],
        'friday' => ['09:00', '19:00'],
        'saturday' => ['09:00', '17:00'],
        'sunday' => null, // Cerrado
    ],

    // Entregas
    'delivery' => [
        'same_day_cutoff' => '14:00', // Hora límite para entrega mismo día
        'free_shipping_minimum' => env('FLORES_FREE_SHIPPING_MIN', 800),
        'shipping_cost' => env('FLORES_SHIPPING_COST', 50),
        'delivery_zones' => [
            'valdivia' => 0, // Gratis
            'santiago' => 0, // Gratis
            'alrededores' => 100,
        ],
        'ciudades' => ['Santiago', 'Valdivia'],
    ],

    // Pedidos
    'orders' => [
        'minimum_amount' => env('FLORES_MIN_ORDER', 200),
        'payment_methods' => ['transfer', 'cash', 'card'],
        'default_status' => 'pending',
    ],

    // Stock
    'stock' => [
        'reservation_time' => 30, // Minutos que se reserva stock
        'low_stock_threshold' => 5,
    ],

    // Reviews
    'reviews' => [
        'require_approval' => true,
        'allow_photos' => true,
        'max_photos' => 3,
    ],

    // SEO
    'seo' => [
        'site_name' => 'Flores D&D',
        'default_title' => 'Flores D&D | Arreglos Florales Artesanales',
        'default_description' => 'Floristería artesanal en Valdivia y Santiago, Chile. Arreglos florales únicos para cada ocasión. Entrega el mismo día.',
    ],

    // Social
    'social' => [
        'facebook' => env('FLORES_FACEBOOK', 'https://facebook.com/floresdyd'),
        'instagram' => env('FLORES_INSTAGRAM', 'https://instagram.com/floresdyd'),
        'tiktok' => env('FLORES_TIKTOK', 'https://tiktok.com/@floresdyd'),
    ],

    // Admin
    'admin' => [
        'notification_email' => env('FLORES_ADMIN_EMAIL', 'admin@floresdyd.com'),
        'items_per_page' => 20,
    ],

];
