<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //drop procedure if exists get_cotization_v2;
        DB::unprepared('DROP PROCEDURE IF EXISTS get_cotization_tributos_v2');
        $sp="CREATE PROCEDURE `get_cotization_tributos_v2`(IN p_id_cotizacion int)
        begin
            
            -- valor flete y valor destino
            set @flete=0.6;
            set @destino=0.4;
            -- Obtener la suma de FOB y FOB valorado
            SELECT
                SUM(Cantidad * Valor_unitario) AS sum_fob,
                SUM(Cantidad * (SELECT get_tribute_value(ID_Producto, 5))) AS sum_fob_valorado
            INTO
                @sum_fob,
                @sum_fob_valorado
            FROM
                carga_consolidada_cotizaciones_detalles_producto
            WHERE
                ID_Cotizacion = p_id_cotizacion;
        
            -- Obtener la suma de CBM total y Peso total
            SELECT
                SUM(CBM_Total) AS cbm_total,
                SUM(Peso_Total) AS peso_total
            INTO
                @cbm_total,
                @peso_total
            FROM
                carga_consolidada_cotizaciones_detalles_proovedor
            WHERE
                ID_Cotizacion = p_id_cotizacion;
        
            -- Calcular el seguro total
            SET @seguro_total = CASE
            WHEN (IF(@sum_fob_valorado = 0, @sum_fob, @sum_fob_valorado) + (SELECT get_cbm_total(@cbm_total, 1) * @flete)) > 5000 THEN 100
            ELSE 50
        END;
            -- Calcular el CIF total
            SET @cif_total = @seguro_total + @sum_fob + (SELECT get_cbm_total(@cbm_total, 1) * @flete);
        
            -- Calcular el CIF valorado total
            SET @cif_valorado_total = CASE
                WHEN @sum_fob_valorado = 0 THEN 0
                ELSE @seguro_total + @sum_fob_valorado + (SELECT get_cbm_total(@cbm_total, 1) * @flete)
            END;
        
            select
                                    cccdp.Nombre_Comercial,
                                    0 Peso,
                                    (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) Total_CBM,
                                    cccdp.Valor_Unitario,
                                    (SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valoracion,
                                    cccdp.Cantidad,
                                    cccdp.Cantidad * cccdp.Valor_unitario AS Valor_FOB,
                                    cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5)) AS Valor_FOB_Valorado,
                                    ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Distribucion,
                                    ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) AS Flete,
                                    ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete*ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) )+(cccdp.Cantidad*cccdp.Valor_Unitario) ),2) Valor_CFR,
                                    CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  <> 0 THEN
                                            (((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                            ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                           ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)))
                                        ELSE (ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        
                                                END AS Valor_CFR_Valorizado,
                                    @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) AS Seguro,
                                    @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                    ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS Valor_CIF,
                                    CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  = 0 then
                                        (@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) +(ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                                ELSE 0
                                            END
                                    END as Valor_CIF_Valorado,
                                    @sum_fob AS sum_fob,
                                    @sum_fob_valorado AS sum_fob_valorado,
                                    @cbm_total AS cbm_total,
                                    @peso_total AS peso_total,
                                    @sum_fob + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) AS cfr_total,
                                    ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) AS cfr,
                                    
                                    
                                    CASE
                                        WHEN @sum_fob_valorado = 0 THEN 0
                                        ELSE @sum_fob_valorado + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)
                                    END AS cfr_valorado_total,
                                    @seguro_total AS seguro_total,
                                   
                                    
                                    @cif_total AS cif_total,
                                    @cif_valorado_total AS cif_valorado_total,
                                     CAST((SELECT get_tribute_value(cccdp.ID_Producto, 1)) AS DECIMAL(10, 2)) AS ad_valorem,
                                    CAST((SELECT get_tribute_value(cccdp.ID_Producto, 2)) AS DECIMAL(10, 2)) AS igv,
                                    CAST((SELECT get_tribute_value(cccdp.ID_Producto, 3)) AS DECIMAL(10, 2)) AS ipm,
                                    CAST((SELECT get_tribute_value(cccdp.ID_Producto, 4)) AS DECIMAL(10, 2)) AS percepcion,
                                    CAST((SELECT get_tribute_value(cccdp.ID_Producto, 6)) AS DECIMAL(10, 2)) AS antidumping,
                                   ROUND((
                                (select get_taxes_calc(
                                        cccdp.ID_Producto,
                                        cccdp.ID_Cotizacion,
                                        1,
                                         CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  = 0 then
                                        (@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) +(ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                                ELSE 0
                                            END
                                    END
                                            )
                                        )
                            ), 2) AS ad_valorem_value,
                            ROUND((
                                (select get_taxes_calc(
                                        cccdp.ID_Producto,
                                        cccdp.ID_Cotizacion,
                                        2,
                                         CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  = 0 then
                                        (@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) +(ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                                ELSE 0
                                            END
                                    END
                                            )
                                        )
                            ), 2) AS igv_value,
                            ROUND((
                                (select get_taxes_calc(
                                        cccdp.ID_Producto,
                                        cccdp.ID_Cotizacion,
                                        3,
                                        CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  = 0 then
                                        (@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) +(ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                                ELSE 0
                                            END
                                    END
                                            )
                                        )
                            ), 2) AS ipm_value,
                            ROUND((
                                (select get_taxes_calc(
                                        cccdp.ID_Producto,
                                        cccdp.ID_Cotizacion,
                                        4,
                                        CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  = 0 then
                                        (@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) +(ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                                ELSE 0
                                            END
                                    END
                                            )
                                        )
                            ), 2) as percepcion_value,
                            ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_de_envio,
                            ROUND((
                                CASE
                                    WHEN @sum_fob_valorado <> 0 THEN
                                        (select get_taxes_calc(
                                        cccdp.ID_Producto,
                                        cccdp.ID_Cotizacion,
                                        -1,
                                         CASE
                                        WHEN @sum_fob_valorado = 0 THEN 0
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                                                ELSE 0
                                            END
                                        END )
                                        )+ CASE
                                        WHEN @sum_fob_valorado = 0 THEN 0
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * (SELECT get_tribute_value(cccdp.ID_Producto, 5))) / NULLIF(@sum_fob_valorado, 0), 2))
                                                ELSE 0
                                            END
                                        END 
                                    ELSE
                                         (select get_taxes_calc(
                                        cccdp.ID_Producto,
                                        cccdp.ID_Cotizacion,
                                        -1,
                                         @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
                                         * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
                                            
                                        ))+@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) + ROUND(((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @flete 
                                         * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) + (cccdp.Cantidad * cccdp.Valor_unitario), 2) 
                                end ),2)+ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) as costo_total,
                                (select Peso_total from carga_consolidada_cotizaciones_detalles_proovedor where ID_Cotizacion=p_id_cotizacion and cccdp.ID_Proveedor=ID_Proveedor) as Peso_Total,
                                
                                ROUND(CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  <> 0 THEN
                                            (((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                            ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                           ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)))
                                        ELSE (ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        
                                        end+ 
                                        (select get_taxes_calc(
                                        cccdp.ID_Producto,
                                        cccdp.ID_Cotizacion,
                                        -1,
                                                         CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  = 0 then
                                        (@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) +(ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                                ELSE 0
                                            END
                                    END
                            
                                            
                                        )) +
                                        
                                            ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente)) * @destino * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2), 2) + (SELECT get_tribute_value(cccdp.ID_Producto, 6)),2 )as Total_Cantidad,
                                ROUND((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente))) Servicio,
                                (select get_taxes_calc(
                                        cccdp.ID_Producto,
                                        cccdp.ID_Cotizacion,
                                        -1,
                                         CASE
                                        WHEN (SELECT get_tribute_value(cccdp.ID_Producto, 5))  = 0 then
                                        (@seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2)) +(ROUND((cccdp.Cantidad * cccdp.Valor_unitario)))	+
                                        ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                        ELSE @seguro_total * ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2) +
                                            CASE
                                                WHEN @sum_fob_valorado <> 0 THEN
                                                    ((SELECT get_tribute_value(cccdp.ID_Producto, 5)) * cccdp.Cantidad) +
                                                    ((SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete) *
                                                        ROUND((cccdp.Cantidad * cccdp.Valor_unitario) / @sum_fob, 2))
                                                ELSE 0
                                            END
                                    END
                                            
                                        )) as taxes,
                                (select cctc.Nombre from carga_consolidada_cotizaciones_cabecera cccc join carga_consolidada_tipo_cliente cctc ON cctc.ID_Tipo_Cliente =cccc.ID_Tipo_Cliente  where ID_Cotizacion=p_id_cotizacion) as tipo_cliente,
                                
                                (select cctc.ID_Tipo_Cliente from carga_consolidada_cotizaciones_cabecera cccc join carga_consolidada_tipo_cliente cctc ON cctc.ID_Tipo_Cliente =cccc.ID_Tipo_Cliente  where ID_Cotizacion=p_id_cotizacion) as ID_Tipo_Cliente,
                                                (select count(*) from carga_consolidada_cotizaciones_detalles_proovedor cccdprove  where cccdprove.ID_Cotizacion =p_id_cotizacion) as count_proveedores,
                                                @cbm_total as CBM_Total
                                                
                
                            from
                                    carga_consolidada_cotizaciones_detalles_producto cccdp
                                WHERE
                                    cccdp.ID_Cotizacion = p_id_cotizacion;
        END";
        DB::unprepared($sp);
        //drop function if exists get_tribute_value
        DB::unprepared('DROP FUNCTION IF EXISTS get_tribute_value');
        $function="CREATE FUNCTION get_tribute_value(p_id_producto int ,tipo_tributo int) RETURNS decimal(10,2)
        begin
                           declare v_value decimal(10,2) default 0;
                       
                        select value into v_value from carga_consolidada_cotizaciones_detalles_tributo cccdt where ID_Producto =p_id_producto
                        and ID_Tipo_Tributo =tipo_tributo
                        ;
                    return v_value;
                END";
        DB::unprepared($function);
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
