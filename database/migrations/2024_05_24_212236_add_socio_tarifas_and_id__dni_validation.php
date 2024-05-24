<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AddSocioTarifasAndIdDniValidation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $alterDetalles="alter table carga_consolidada_cotizaciones_detalle add column Email varchar(50);";
        $alterDetalles2="alter table carga_consolidada_cotizaciones_detalle add column Empresa varchar(50) ;";
        $alterDetalles3="alter table carga_consolidada_cotizaciones_detalle add column Ruc varchar(50)";
        $insertSocioTarifas="insert into carga_consolidada_cbm_tarifas(id_tipo_tarifa,id_tipo_cliente,limite_inf,limite_sup,currency,tarifa,created_at)
        values(1,3,0.1,0.99,'USD',250,now()),
        (2,3,1,999999,'USD',250,now());";
        $updateErrorTarifas="UPDATE carga_consolidada_cbm_tarifas
        SET limite_sup  = CASE
            WHEN limite_sup = 0.50 THEN 0.59
            WHEN limite_sup = 1.00 THEN 1.09
            WHEN limite_sup = 2.00 THEN 2.09
            WHEN limite_sup = 3.00 THEN 3.09
            WHEN limite_sup = 4.00 THEN 4.09
            ELSE limite_sup
        END
        WHERE limite_sup IN (0.50, 1.00, 2.00, 3.00, 4.00);";
        DB::statement($insertSocioTarifas);
        DB::statement($updateErrorTarifas);
        DB::statement($alterDetalles);
        DB::statement($alterDetalles2);
        DB::statement($alterDetalles3);

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
