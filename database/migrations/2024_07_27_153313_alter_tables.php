<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * alter table agente_compra_pedido_detalle_producto_proveedor add column total_box decimal(10,2);
alter table agente_compra_pedido_detalle_producto_proveedor add column total_cbm decimal(10,2);
alter table agente_compra_pedido_detalle_producto_proveedor add column total_kg decimal(10,2);
alter table agente_compra_pedido_detalle_producto_proveedor add column almacen_foto1 text;
alter table agente_compra_pedido_detalle_producto_proveedor add column almacen_foto2 text;
alter table agente_compra_pedido_detalle_producto_proveedor add column almacen_estado enum("PENDIENTE","RECIBIDO") default "PENDIENTE";
         * 
         * 
         */
        $sp = "alter table agente_compra_pedido_detalle_producto_proveedor add column total_box decimal(10,2);
            alter table agente_compra_pedido_detalle_producto_proveedor add column total_cbm decimal(10,2);
            alter table agente_compra_pedido_detalle_producto_proveedor add column total_kg decimal(10,2);
            alter table agente_compra_pedido_detalle_producto_proveedor add column almacen_foto1 text;
            alter table agente_compra_pedido_detalle_producto_proveedor add column almacen_foto2 text;
            alter table agente_compra_pedido_detalle_producto_proveedor add column almacen_estado enum('PENDIENTE','RECIBIDO') default 'PENDIENTE';";

        DB::unprepared($sp);
        $sp = "alter table agente_compra_pedido_detalle_producto_proveedor add column total_box decimal(10,2);";
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
