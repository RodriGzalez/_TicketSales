<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('gateway'); // ej. 'stripe', 'mercadopago', 'paypal'
            $table->string('gateway_transaction_id')->nullable()->index(); // ID único de la pasarela
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MXN');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->json('payload')->nullable(); // Respuesta completa del webhook para auditoría contable
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};