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
        $table->foreignId('ticket_type_id')->nullable()->constrained('ticket_types')->onDelete('cascade')->after('event_id');
    });
}

public function down()
{
    Schema::table('tickets', function (Blueprint $table) {
        if (!Schema::hasColumn('tickets', 'quantity')) {
            $table->integer('quantity')->default(1)->after('event_id');
        }
});
}
};
