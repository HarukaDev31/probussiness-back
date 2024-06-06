<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InitProcedures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $getCBMTotal="CREATE  FUNCTION `get_cbm_total`(id_cotizacion int, cbm decimal(10,2),tipo_cliente int) RETURNS decimal(10,2)
        begin
            declare precio decimal(10,2)  default 0;
            declare v_tarifa decimal(10,2) default 0;
           	declare v_cotizacion_created_at timestamp default now();
           	select created_at into v_cotizacion_created_at from carga_consolidada_cotizaciones_cabecera where ID_Cotizacion=id_cotizacion limit 1 ;
            select if(id_tipo_tarifa=1,tarifa,tarifa*cbm)  into v_tarifa from carga_consolidada_cbm_tarifas  ccbt
            where (cbm >= ccbt.limite_inf and cbm<=ccbt.limite_sup
            and ccbt.id_tipo_cliente=tipo_cliente and
           	date(ccbt.created_at)>=ifnull(v_cotizacion_created_at,'1999-01-01')
            ) limit 1;
            return v_tarifa;
        END";
        $getCotizationTributos="CREATE PROCEDURE `get_cotization_tributos_v2`( IN p_id_cotizacion int )
        begin
                declare v_t_cliente int default 1;
        
                -- valor flete y valor destino
                set @flete=0.6;
                set @destino=0.4;
                select ID_Tipo_Cliente into v_t_cliente from carga_consolidada_cotizaciones_cabecera cccc where cccc.ID_Cotizacion=p_id_cotizacion;
                -- Obtener la suma de FOB y FOB valorado
                SELECT
                    SUM(Cantidad * Valor_unitario) AS sum_fob,
                    SUM(Cantidad * (SELECT get_tribute_value(ID_Producto, 5))) AS sum_fob_valorado,
                    SUM(Cantidad) as total_cantidad
                INTO
                    @sum_fob,
                    @sum_fob_valorado,
                    @total_cantidad
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
                WHEN (IF(@sum_fob_valorado = 0, @sum_fob, @sum_fob_valorado) + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)) > 5000 THEN 100
                ELSE 50
            END;
                -- Calcular el CIF total
                SET @cif_total = @seguro_total + @sum_fob + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete);
            
                -- Calcular el CIF valorado total
                SET @cif_valorado_total = CASE
                    WHEN @sum_fob_valorado = 0 THEN 0
                    ELSE @seguro_total + @sum_fob_valorado + (SELECT get_cbm_total(p_id_cotizacion,@cbm_total,v_t_cliente) * @flete)
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
                    (SELECT get_tribute_value(cccdp.ID_Producto, 1)) AS ad_valorem,
                    (SELECT get_tribute_value(cccdp.ID_Producto, 2)) AS igv,
                    (SELECT get_tribute_value(cccdp.ID_Producto, 3)) AS ipm,
                    (SELECT get_tribute_value(cccdp.ID_Producto, 4)) AS percepcion,
                    (SELECT get_tribute_value(cccdp.ID_Producto, 6)) AS antidumping,
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
                (select cctc.Nombre from carga_consolidada_cotizaciones_cabecera cccc join carga_consolidada_tipo_cliente cctc ON cctc.ID_Tipo_Cliente =cccc.ID_Tipo_Cliente  where ID_Cotizacion=p_id_cotizacion) as tipo_cliente
            from
                    carga_consolidada_cotizaciones_detalles_producto cccdp
                WHERE
                    cccdp.ID_Cotizacion = p_id_cotizacion;
            END;";

            $getTaxCalc="CREATE  FUNCTION `get_taxes_calc`(p_producto_id INT,
            p_cotizacion_id INT,
            p_tributo_id INT,
            v_valor_cif decimal(10,2)
        ) RETURNS decimal(10,2)
        BEGIN
            DECLARE v_distribucion DECIMAL(10, 2);
            DECLARE v_ad_honorem DECIMAL(10, 2);
            DECLARE v_igv DECIMAL(10, 2);
            DECLARE v_ipm DECIMAL(10, 2);
            DECLARE v_percepcion DECIMAL(10, 2);
            DECLARE v_valor_tributo DECIMAL(10, 2);
        
        
            -- Obtener los valores de los tributos
            SELECT 
                get_tribute_value(p_producto_id, 1),
                get_tribute_value(p_producto_id, 2),
                get_tribute_value(p_producto_id, 3),
                get_tribute_value(p_producto_id, 4)
            INTO 
                v_ad_honorem,
                v_igv,
                v_ipm,
                v_percepcion;
        
            -- Calcular el valor del CIF
        
            -- Calcular el valor del tributo según el tipo de tributo
            CASE p_tributo_id
                WHEN 1 THEN
                    SET v_valor_tributo = v_valor_cif * v_ad_honorem / 100;
                WHEN 2 THEN
                    SET v_valor_tributo = (v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_igv / 100;
                WHEN 3 THEN
                    SET v_valor_tributo = (v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_ipm / 100;
                WHEN 4 THEN
                    SET v_valor_tributo = (v_valor_cif + (v_valor_cif * v_ad_honorem / 100)
                        + ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_igv / 100)
                        + ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_ipm / 100))
                        * v_percepcion / 100;
                WHEN -1 THEN
                    SET v_valor_tributo =((v_valor_cif * v_ad_honorem / 100) +
                    ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_igv / 100) +
                    ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_ipm / 100))+
                    ((v_valor_cif * v_ad_honorem / 100) +
                    ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_igv / 100) +
                    ((v_valor_cif + (v_valor_cif * v_ad_honorem / 100)) * v_ipm / 100) +v_valor_cif)*(v_percepcion/100);
                ELSE
                    SET v_valor_tributo = 0;
            END CASE;
        
            RETURN v_valor_tributo;
        END;";
        $getTributeValue="CREATE  FUNCTION `get_tribute_value`(p_id_producto int ,tipo_tributo int) RETURNS int(11)
        begin
            declare v_value int default 0;
                select value into v_value from carga_consolidada_cotizaciones_detalles_tributo cccdt where ID_Producto =p_id_producto
                and ID_Tipo_Tributo =tipo_tributo
                ;
            return v_value;
        END;";
        //drop if exists 

        DB::unprepared($getCBMTotal);
        DB::unprepared($getCotizationTributos);
        DB::unprepared($getTaxCalc);
        DB::unprepared($getTributeValue);
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
