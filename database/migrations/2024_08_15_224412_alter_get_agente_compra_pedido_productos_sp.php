<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class AlterGetAgenteCompraPedidoProductosSp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS get_agente_compra_pedido_productos');
        $sp = "CREATE PROCEDURE get_agente_compra_pedido_productos(in p_id_producto int)
        begin
	select
	*,
    (select cotizacionCode from agente_compra_pedido_cabecera where ID_Pedido_Cabecera=p_id_producto) as cotizacionCode
	from agente_compra_pedido_detalle acpd
	join agente_compra_pedido_detalle_producto_proveedor acpdpp on acpdpp.ID_Pedido_Cabecera=p_id_producto
	and acpd.ID_Pedido_Detalle=acpdpp.ID_Pedido_Detalle
	where acpd.ID_Pedido_Cabecera=p_id_producto and acpdpp.Nu_Selecciono_Proveedor=1;
        END";
        DB::unprepared($sp);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
