<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->string('payment_method'); 
        // Opciones sugeridas: 'card', 'cash_on_delivery', 'not_specified'
    });
}

public function down()
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropColumn('payment_method');
    });
}
};
