<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // El cliente que lo aparta
            $table->foreignId('event_id')->constrained()->onDelete('cascade'); // El evento al que pertenece
            $table->string('ticket_code')->unique(); // Código único o folio (ej. GF-XXXXX)
            $table->enum('status', ['reserved', 'paid pending','paid', 'liquidate','cancelled'])->default('reserved'); // Control de pago contra entrega
            $table->decimal('price', 10, 2)->default(0.00); // Opcional por si el evento tiene costo
            $table->timestamp('expires_at')->nullable(); // Para el tiempo límite de pago contra entrega si aplica
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};