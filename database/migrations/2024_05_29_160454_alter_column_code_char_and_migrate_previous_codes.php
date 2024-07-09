<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AlterColumnCodeCharAndMigratePreviousCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $alter="ALTER TABLE carga_consolidada_cotizaciones_cabecera MODIFY CotizacionCode CHAR(8);";
        $update="UPDATE carga_consolidada_cotizaciones_cabecera
        SET CotizacionCode = CONCAT('2405', SUBSTRING(CotizacionCode, 3));";
        DB::statement($alter);
        DB::statement($update);
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
