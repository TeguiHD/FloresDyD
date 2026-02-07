<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Limpiar reservas de stock expiradas cada 5 minutos
Schedule::command('stock:clean-reservations')->everyFiveMinutes();

// Limpiar carritos abandonados cada hora
Schedule::command('cart:clean-abandoned')->hourly();

// Enviar recordatorio de pedidos pendientes cada 30 minutos
Schedule::command('orders:send-reminders')->everyThirtyMinutes();

// Generar sitemap diariamente
Schedule::command('sitemap:generate')->daily();

// Limpiar logs viejos semanalmente
Schedule::command('logs:clean')->weekly();
