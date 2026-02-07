<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Popups gestionables
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            
            // CTA
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            
            // Configuración de visualización
            $table->enum('trigger', ['page_load', 'exit_intent', 'scroll', 'time_delay'])->default('page_load');
            $table->unsignedInteger('trigger_value')->nullable(); // segundos o % scroll
            $table->json('show_on_pages')->nullable(); // ['/', '/coleccion', '/ocasiones']
            $table->boolean('show_once_per_session')->default(true);
            $table->boolean('show_once_per_user')->default(false);
            
            // Validez
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Estadísticas
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('clicks_count')->default(0);
            
            $table->timestamps();
            
            $table->index(['is_active', 'starts_at', 'expires_at']);
        });

        // Banner de promoción (parte superior)
        Schema::create('promo_banners', function (Blueprint $table) {
            $table->id();
            $table->string('text'); // "🎁 Promoción especial: 20% en Rosas"
            $table->string('icon')->nullable(); // Emoji o icono

            // Countdown
            $table->boolean('has_countdown')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // CTA
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();

            // Configuración
            $table->json('show_on_pages')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_dismissible')->default(true);

            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at', 'sort_order']);
        });

        // Configuraciones del sitio
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general'); // general, seo, social, payment
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json, text
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false); // Para datos sensibles
            $table->timestamps();
            
            $table->index('group');
        });

        // Redes sociales
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // facebook, instagram, whatsapp, etc.
            $table->string('url');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('open_in_new_tab')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });

        // Audit Log inmutable (blockchain-like)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            
            // Inmutabilidad (hash chain)
            $table->string('previous_hash', 64)->nullable();
            $table->string('current_hash', 64)->unique();
            
            $table->timestamp('created_at');
            
            // Índices
            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'action']);
            $table->index('created_at');
        });

        // Notificaciones del sistema
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index('read_at');
        });

        // Registro de emails enviados
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email_to');
            $table->string('email_type'); // welcome, order_confirmation, status_update, etc.
            $table->string('subject');
            $table->string('mailable_class');
            
            // Tracking
            $table->enum('status', ['queued', 'sent', 'failed', 'bounced'])->default('queued');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            
            // Referencia
            $table->string('related_model_type')->nullable();
            $table->unsignedBigInteger('related_model_id')->nullable();
            
            $table->timestamps();
            
            $table->index(['email_type', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('promo_banners');
        Schema::dropIfExists('popups');
    }
};
