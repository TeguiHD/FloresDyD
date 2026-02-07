<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Hasheado con Argon2id + Pepper
            
            // Datos sensibles CIFRADOS en BD (AES-256-GCM)
            $table->text('phone_encrypted')->nullable(); // Teléfono cifrado
            $table->text('address_encrypted')->nullable(); // Dirección cifrada
            
            // Trust Score para sistema anti-fraude
            $table->unsignedTinyInteger('trust_score')->default(50); // 0-100
            $table->unsignedSmallInteger('successful_orders')->default(0);
            $table->boolean('is_whitelisted')->default(false);
            
            // Seguridad
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->string('last_login_ip', 45)->nullable(); // IPv6 compatible
            $table->text('last_login_fingerprint')->nullable();
            
            // MFA/2FA
            $table->text('two_factor_secret')->nullable(); // Cifrado
            $table->text('two_factor_recovery_codes')->nullable(); // Cifrado
            $table->timestamp('two_factor_confirmed_at')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('trust_score');
            $table->index('is_whitelisted');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token'); // Hasheado
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
