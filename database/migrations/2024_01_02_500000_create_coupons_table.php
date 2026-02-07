<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cupones de descuento
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Tipo de descuento
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->unsignedInteger('value'); // Porcentaje o monto en centavos
            
            // Restricciones
            $table->unsignedInteger('min_purchase_amount')->nullable(); // Monto mínimo
            $table->unsignedInteger('max_discount_amount')->nullable(); // Descuento máximo
            $table->unsignedInteger('max_uses')->nullable(); // Usos totales permitidos
            $table->unsignedInteger('max_uses_per_user')->default(1);
            $table->unsignedInteger('uses_count')->default(0);
            
            // Vigencia
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Estado
            $table->boolean('is_active')->default(true);
            
            // Restricciones por producto/categoría
            $table->json('applicable_products')->nullable(); // IDs de productos
            $table->json('applicable_categories')->nullable(); // IDs de categorías
            $table->json('excluded_products')->nullable();
            
            // Restricciones por usuario
            $table->boolean('first_purchase_only')->default(false);
            $table->json('applicable_users')->nullable(); // IDs de usuarios específicos
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['code', 'is_active']);
            $table->index(['starts_at', 'expires_at']);
        });

        // Registro de uso de cupones
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable();
            $table->unsignedInteger('discount_applied');
            $table->timestamps();
            
            $table->index(['coupon_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
    }
};
