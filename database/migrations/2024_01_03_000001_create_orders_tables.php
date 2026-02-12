<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pedidos
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // FDD-2026-00001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Estado del pedido
            $table->enum('status', [
                'pending_payment',      // Esperando comprobante
                'pending_review',       // Comprobante subido, esperando revisión
                'payment_verified',     // Pago verificado
                'preparing',            // En preparación
                'ready_for_delivery',   // Listo para envío
                'in_delivery',          // En camino
                'delivered',            // Entregado
                'cancelled',            // Cancelado
                'refunded'              // Reembolsado
            ])->default('pending_payment');
            
            // Datos de contacto CIFRADOS
            $table->text('customer_name_encrypted');
            $table->text('customer_email_encrypted');
            $table->text('customer_phone_encrypted');
            
            // Dirección de entrega CIFRADA
            $table->text('delivery_address_encrypted');
            $table->text('delivery_city_encrypted');
            $table->text('delivery_notes_encrypted')->nullable();
            
            // Fecha de entrega deseada
            $table->date('delivery_date')->nullable();
            $table->string('delivery_time_slot')->nullable(); // "09:00-12:00"
            
            // Tarjeta personalizada CIFRADA
            $table->text('card_message_encrypted')->nullable();
            $table->string('card_recipient')->nullable();
            $table->string('card_sender')->nullable();
            
            // Totales
            $table->unsignedInteger('subtotal'); // Precio en CLP directo
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('delivery_fee')->default(0);
            $table->unsignedInteger('total');
            
            // Cupón aplicado
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('coupon_code')->nullable();
            
            // Anti-fraude
            $table->unsignedTinyInteger('fraud_score')->default(50); // 0-100
            $table->json('fraud_factors')->nullable(); // Detalle del scoring
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('fingerprint_hash', 64)->nullable(); // SHA-256
            
            // Timestamps importantes
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->text('admin_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('status');
            $table->index('fraud_score');
            $table->index(['user_id', 'status']);
            $table->index('delivery_date');
            $table->index('created_at');
        });

        // Items del pedido
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            
            $table->string('product_name'); // Snapshot del nombre
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price'); // Precio al momento de compra
            $table->unsignedInteger('total_price');
            
            $table->timestamps();
        });

        // Comprobantes de pago
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            
            // Archivo
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedInteger('file_size');
            
            // Hash para detectar duplicados
            $table->string('file_hash', 64)->unique(); // SHA-256
            
            // Datos del comprobante (ingresados por usuario)
            $table->text('transaction_code_encrypted')->nullable(); // Cifrado
            $table->unsignedInteger('declared_amount')->nullable();
            $table->date('transaction_date')->nullable();
            $table->string('bank_origin')->nullable();
            
            // Verificación
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Metadata del archivo (para anti-fraude)
            $table->json('file_metadata')->nullable(); // EXIF, etc.
            
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index('file_hash');
        });

        // Historial de estados del pedido (para timeline)
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            
            // Para notificaciones
            $table->boolean('customer_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('payment_proofs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
