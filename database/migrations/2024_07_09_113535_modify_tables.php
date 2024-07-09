<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class ModifyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query="ALTER TABLE agente_compra_pedido_detalle 
        add column caja_master_URL TEXT(21845) NULL,
        ADD COLUMN empaque_URL TEXT NULL,
        ADD COLUMN vim_motor_URL TEXT NULL,
        ADD COLUMN notas_rotulado TEXT NULL,
        ADD COLUMN product_code VARCHAR(50) NULL,
        ADD COLUMN Txt_Producto_Ingles TEXT NULL,
        ADD COLUMN Txt_Description_Ingles TEXT NULL;";
        DB::unprepared($query);
        $query="alter table agente_compra_pedido_detalle_producto_proveedor
        ADD COLUMN main_photo TEXT NULL,
        ADD COLUMN secondary_photo TEXT NULL,
        ADD COLUMN terciary_photo TEXT NULL,
        ADD COLUMN primary_video TEXT NULL,
        ADD COLUMN secondary_video TEXT NULL,
        ADD COLUMN unidad_medida CHAR(2) NULL,
        ADD COLUMN kg_box DECIMAL(10,2) NULL,
        ADD COLUMN fecha_entrega DATE NULL;";
        DB::unprepared($query);
        $query="alter table agente_compra_pedido_cabecera
        ADD COLUMN file_cotizacion TEXT NULL,
        ADD COLUMN total_rmb DECIMAL(10,2) NULL,
        ADD COLUMN cotizacionCode VARCHAR(7) NULL,
        ADD COLUMN id_estado_orden_compra INT NULL,
        ADD COLUMN ordenCotizacion VARCHAR(50) NULL;";
                DB::unprepared($query);
        $query="
        CREATE TABLE agente_compra_coordination_supplier (
            id_coordination INT NOT NULL,
            id_pedido INT UNSIGNED NOT NULL,
            id_supplier INT NOT NULL,
            total DECIMAL(10,2) NULL,
            dead_line DATETIME NULL,
            pago_1_value DECIMAL(10,2) NULL,
            pago_1_URL TEXT(16383) NULL,
            pago_2_value DECIMAL(10,2) NULL,
            pago_2_URL TEXT(16383) NULL,
            estado CHAR(9) NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        );";
        DB::unprepared($query);

        $query="
            CREATE TABLE payment_types (
                id INT NOT NULL,
                name VARCHAR(255) NULL
            );
            ";
        DB::unprepared($query);
       

        $query="INSERT INTO payment_types
                (id, name)
                VALUES(1, 'garantia');
                INSERT INTO payment_types
                (id, name)
                VALUES(2, 'normal');
                INSERT INTO payment_types
                (id, name)
                VALUES(3, 'liquidacion');
                ";
        DB::unprepared($query);
        $query="
            CREATE TABLE payments_agente_compra_pedido (
                id INT NOT NULL,
                id_pedido INT UNSIGNED NOT NULL,
                file_url TEXT(16383) NULL,
                value DECIMAL(10,2) NULL,
                created_at DATETIME NULL,
                id_type_payment INT NULL
            )
            ";
        DB::unprepared($query);
        $query="
            CREATE TABLE agente_compra_order_steps (
                id INT NOT NULL,
                id_pedido INT UNSIGNED NOT NULL,
                id_permision_role INT NULL,
                id_order INT NULL,
                name VARCHAR(100) NULL,
                status CHAR(9) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                iconURL TEXT(16383) NULL
            )

            ";
        DB::unprepared($query);
        $sp="CREATE PROCEDURE get_suppliers_products(IN p_id_pedido INT)
            BEGIN
                SELECT 
                    s.name,
                    s.phone,
                    s.id_supplier,
                    acc.*,
                    CONCAT('[', 
                        (
                            SELECT GROUP_CONCAT(
                                CONCAT(
                                    '{\"ID_Pedido_Detalle\":', a2.ID_Pedido_Detalle,
                                    ',\"nombre_producto\":\"', IFNULL(a2.Txt_Producto, ''),
                                    '\",\"product_code\":\"', IFNULL(a2.product_code, ''),
                                    '\",\"qty_product\":\"', IFNULL(a2.Qt_Producto, ''),
                                    '\",\"price_product\":\"', IFNULL(b.Ss_Precio, ''),
                                    '\",\"total_producto\":\"', IFNULL(b.Ss_Precio * a2.Qt_Producto, ''),
                                    '\",\"delivery\":\"', IFNULL(b.Ss_Costo_Delivery, ''),
                                    '\",\"tentrega\":\"', IFNULL( DATE_ADD(NOW(), INTERVAL Nu_Dias_Delivery DAY),now()),
                                    '\",\"pago1\":\"', IFNULL(b.Ss_Precio, ''),
                                    '\",\"pago1URL\":\"', IFNULL(NULL, ''),
                                    '\",\"pago2\":\"', IFNULL(b.Ss_Precio, ''),
                                    '\",\"pago2URL\":\"', IFNULL(NULL, ''),
                                    '\",\"estado\":\"', IFNULL(b.Ss_Precio, ''),
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
                HAVING detalles IS NOT NULL;
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
