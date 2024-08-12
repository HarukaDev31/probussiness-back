<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AddInspectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::statement('ALTER TABLE agente_compra_pedido_detalle_producto_proveedor ADD COLUMN personal_china_inspeccion_estado ENUM("PENDIENTE","INSPECCIONADO") DEFAULT "PENDIENTE"');
        DB::statement('ALTER TABLE agente_compra_pedido_detalle_producto_proveedor ADD COLUMN inspeccion_foto1 TEXT');
        DB::statement('ALTER TABLE agente_compra_pedido_detalle_producto_proveedor ADD COLUMN inspeccion_foto2 TEXT');
        DB::statement('ALTER TABLE agente_compra_pedido_detalle_producto_proveedor ADD COLUMN inspeccion_foto3 TEXT');
        DB::statement('ALTER TABLE agente_compra_pedido_detalle_producto_proveedor ADD COLUMN inspeccion_video1 TEXT');
        DB::statement('ALTER TABLE agente_compra_pedido_detalle_producto_proveedor ADD COLUMN inspeccion_video2 TEXT');
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
