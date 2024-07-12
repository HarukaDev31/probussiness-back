<?php

use Illuminate\Database\Migrations\Migration;

class SpSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sp = "CREATE PROCEDURE `get_suppliers_products`(IN p_id_pedido INT,in p_id_supplier int)
BEGIN
    SELECT
        s.name,
        s.phone,
        s.id_supplier,
        acc.*,
        acpc.cotizacionCode,
        CONCAT('[',
            (
                SELECT GROUP_CONCAT(
                    CONCAT(
                        '{\"ID_Pedido_Detalle\":', a2.ID_Pedido_Detalle,
                        ',\"nombre_producto\":\"', IFNULL(a2.Txt_Producto, ''),
                        '\",\"ID_Pedido_Detalle_Producto_Proveedor\":\"', IFNULL(b.ID_Pedido_Detalle_Producto_Proveedor, ''),
                        '\",\"product_code\":\"', IFNULL(a2.product_code, ''),
                        '\",\"qty_product\":\"', IFNULL(a2.Qt_Producto, ''),
                        '\",\"price_product\":\"', IFNULL(b.Ss_Precio, ''),
                        '\",\"total_producto\":\"', IFNULL(b.Ss_Precio * a2.Qt_Producto, ''),
                        '\",\"delivery\":\"', IFNULL(b.Nu_Dias_Delivery, ''),
                        '\",\"tentrega\":\"', IFNULL( b.fecha_entrega,now()),
                        '\",\"shipping_cost\":\"', IFNULL(b.Ss_Costo_Delivery, ''),
                       	'\",\"pago1\":\"', IFNULL(acc.pago_1_value, ''),
                        '\",\"pago1URL\":\"', IFNULL(acc.pago_1_URL, ''),
                        '\",\"pago2\":\"', IFNULL(acc.pago_2_value, ''),
                        '\",\"pago2URL\":\"', IFNULL(acc.pago_2_URL, ''),
                        '\",\"imagenURL\":\"',a2.Txt_Url_Imagen_Producto,
                        '\",\"descripcion\":\"',a2.Txt_Descripcion,
                        '\",\"estado\":\"', IFNULL(b.Ss_Precio, ''),
                        '\",\"unidad_medida\":\"', IFNULL(b.unidad_medida, ''),

                        '\"}'
                    ) SEPARATOR ','
                )
                FROM agente_compra_pedido_detalle a2
                JOIN agente_compra_pedido_detalle_producto_proveedor b
                    ON b.ID_Pedido_Detalle = a2.ID_Pedido_Detalle

                WHERE a2.ID_Pedido_Cabecera = p_id_pedido and b.Nu_Selecciono_Proveedor=1
                AND b.ID_Entidad_Proveedor = s.id_supplier
            )
        ,']') AS detalles
    FROM suppliers s
    join agente_compra_coordination_supplier acc on acc.id_supplier=s.id_supplier and acc.id_pedido=p_id_pedido
    join agente_compra_pedido_cabecera acpc on acpc.ID_Pedido_Cabecera=acc.id_pedido
     WHERE (p_id_supplier IS NULL OR s.id_supplier = p_id_supplier)

    HAVING detalles IS NOT NULL;
END";
    $drop = "DROP PROCEDURE IF EXISTS `get_suppliers_products`";
    DB::unprepared($drop);
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
