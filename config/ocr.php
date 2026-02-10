<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OCR Habilitado
    |--------------------------------------------------------------------------
    | Activa o desactiva el análisis OCR de comprobantes de pago.
    | Si está desactivado, los comprobantes quedan en estado "pending"
    | para revisión manual del administrador.
    */
    'enabled' => env('OCR_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | OCR.space API Key
    |--------------------------------------------------------------------------
    | Obtener gratis en: https://ocr.space/ocrapi/freekey
    | Plan gratuito: 25,000 requests/mes, archivos hasta 1 MB.
    | No usar "helloworld" en producción (es solo para pruebas).
    */
    'api_key' => env('OCR_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Motor OCR (Engine)
    |--------------------------------------------------------------------------
    | 1 = Rápido, soporta muchos idiomas, bueno para documentos limpios.
    | 2 = Mejor con caracteres especiales ($, @, €), autodetección de idioma.
    |     Mejor para texto sobre fondos confusos, fotos de pantalla, etc.
    | 3 = Mejor calidad general, soporta 200+ idiomas, reconoce escritura
    |     a mano y tablas. Más lento para archivos grandes.
    |
    | Recomendado para comprobantes: Engine 2 (mejor con montos y símbolos $).
    */
    'engine' => env('OCR_ENGINE', 2),

    /*
    |--------------------------------------------------------------------------
    | Idioma OCR
    |--------------------------------------------------------------------------
    | Código de 3 letras. Engine 2 soporta "auto" para autodetección.
    | Español = "spa", Inglés = "eng".
    | Engine 3 solo acepta "auto".
    | Ver lista completa: https://ocr.space/ocrapi
    */
    'language' => env('OCR_LANGUAGE', 'spa'),

    /*
    |--------------------------------------------------------------------------
    | Confianza mínima (%)
    |--------------------------------------------------------------------------
    | Umbral mínimo de confianza para considerar válido el resultado.
    | Valores por debajo generan flag "low_confidence".
    */
    'min_confidence' => env('OCR_MIN_CONFIDENCE', 60),

    /*
    |--------------------------------------------------------------------------
    | Hits mínimos de keywords
    |--------------------------------------------------------------------------
    | Cantidad mínima de palabras clave que deben aparecer en el texto
    | para que se considere un comprobante legítimo.
    */
    'min_keyword_hits' => env('OCR_MIN_KEYWORD_HITS', 2),

    /*
    |--------------------------------------------------------------------------
    | Palabras clave de comprobantes
    |--------------------------------------------------------------------------
    | Keywords que se buscan en el texto extraído para validar que
    | la imagen corresponde a un comprobante de pago real.
    */
    'keywords' => [
        'transferencia',
        'comprobante',
        'banco',
        'pago',
        'deposito',
        'depósito',
        'saldo',
        'cuenta',
        'rut',
        'clabe',
        'referencia',
        'operacion',
        'operación',
        'monto',
        'fecha',
        'trx',
        'transaccion',
        'transacción',
        'exitosa',
        'aprobada',
        'destinatario',
        'beneficiario',
    ],
];
