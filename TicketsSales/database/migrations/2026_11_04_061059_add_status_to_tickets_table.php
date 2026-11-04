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
    Schema::table('tickets', function (Blueprint $table) {
        $table->enum('status', ['reserved', 'paid', 'cancelled'])->default('reserved')->after('ticket_code');
    });
}

public function down(): void
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
