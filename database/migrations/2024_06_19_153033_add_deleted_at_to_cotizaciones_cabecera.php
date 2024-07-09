<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AddDeletedAtToCotizacionesCabecera extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query="alter table carga_consolidada_cotizaciones_cabecera  add column deleted_at date;";
        DB::statement($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotizaciones_cabecera', function (Blueprint $table) {
            //
        });
    }
}
