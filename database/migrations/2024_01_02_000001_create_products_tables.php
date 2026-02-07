<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categorías de productos
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Emoji o icono
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('show_in_navbar')->default(true);
            $table->boolean('show_in_home')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Estadísticas
            $table->unsignedInteger('views_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'is_featured']);
            $table->index('sort_order');
        });

        // Productos
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            
            // Precios
            $table->unsignedInteger('price'); // En centavos (CLP)
            $table->unsignedInteger('compare_price')->nullable(); // Precio anterior
            $table->unsignedTinyInteger('discount_percentage')->nullable();
            
            // Stock
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('reserved_stock')->default(0); // Reservas temporales
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->boolean('track_stock')->default(true);
            
            // Estado
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(true);
            
            // Sesgos psicológicos - Atributos de venta
            $table->boolean('has_fresh_guarantee')->default(true);
            $table->boolean('has_free_delivery')->default(false);
            $table->string('free_delivery_city')->nullable();
            $table->boolean('has_personalized_card')->default(false);
            $table->json('custom_badges')->nullable(); // Badges personalizados
            
            // Imágenes
            $table->string('main_image');
            $table->json('gallery_images')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Estadísticas
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('sales_count')->default(0);
            
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Índices optimizados
            $table->index(['is_active', 'is_featured']);
            $table->index(['category_id', 'is_active']);
            $table->index('price');
            $table->index('views_count');
            $table->index('sales_count');
        });

        // Reservas temporales de stock
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->unsignedInteger('quantity');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('expires_at');
            $table->index(['product_id', 'expires_at']);
        });

        // Log de cambios de stock
        Schema::create('product_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['add', 'remove', 'reserve', 'release', 'sale', 'adjustment']);
            $table->integer('quantity'); // Puede ser negativo
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_logs');
        Schema::dropIfExists('stock_reservations');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
