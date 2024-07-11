<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sp="CREATE PROCEDURE `get_agente_compra_pedido_productos`(in p_id_producto int)
            begin
                select * from agente_compra_pedido_detalle acpd where acpd.ID_Pedido_Cabecera=p_id_producto;
            end
                ";
        DB::unprepared($sp);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sp');
    }
}
