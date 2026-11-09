<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            // Relación con el administrador que hizo la acción
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->string('action'); // Ej: 'CREATED', 'UPDATED', 'DELETED', 'LOGIN'
            $table->string('module'); // Ej: 'Events', 'Admins', 'Tickets'
            $table->text('description'); // Detalle legible del cambio
            $table->json('changes')->nullable(); // Para guardar los valores viejos y nuevos
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
