<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPagosNotasColumnToAgenteCompraPedidoCabeceraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agente_compra_pedido_cabecera', function (Blueprint $table) {
            $table->text('notas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agente_compra_pedido_cabecera', function (Blueprint $table) {
            //
        });
    }
}
