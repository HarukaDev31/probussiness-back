<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAgenteCompraPedidoDetalleProductoProveedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agente_compra_pedido_detalle_producto_proveedor', function (Blueprint $table) {
            $table->text('almacen_notas')->nullable();
            $table->text('empleado_china_notas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agente_compra_pedido_detalle_producto_proveedor', function (Blueprint $table) {
            //
        });
    }
}
