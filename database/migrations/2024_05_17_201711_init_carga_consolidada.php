<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InitCargaConsolidada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableCabeceraQuery="
        CREATE TABLE carga_consolidada_cotizaciones_cabecera (
            ID_Cotizacion INT NOT NULL AUTO_INCREMENT,
            Fe_Creacion DATE NOT NULL,
            N_Cliente TEXT(65535) NULL,
            Empresa TEXT(65535) NULL,
            Cotizacion DECIMAL(10,2) NULL,
            ID_Tipo_Cliente INT NOT NULL,
            Cotizacion_Status enum('Pendiente','Cotizado') NULL default 'Pendiente',
            PRIMARY KEY (ID_Cotizacion)
        )";
        $tableTipoTributoTable="CREATE TABLE tipo_carga_consolidada_cotizaciones_tributo (
            ID_Tipo_Tributo INT NOT NULL AUTO_INCREMENT,
            Nombre VARCHAR(255) NULL,
            table_key VARCHAR(50) NULL,
            PRIMARY KEY (ID_Tipo_Tributo)
        );" ;
        $insertIntoTipoTributo="INSERT INTO tipo_carga_consolidada_cotizaciones_tributo
        ( Nombre, table_key)
        VALUES( 'Ad Valorem', 'ad-valorem'),
        ('IGV', 'igv'),
        ('IPM', 'ipm'),
        ('Percepcion', 'percepcion'),
        ('Valoracion', 'valoracion'),
        ('AntiDumping', 'antidumping');

       ";
       $tableDetallesProveedor="CREATE TABLE carga_consolidada_cotizaciones_detalles_proovedor (
        ID_Proveedor INT NOT NULL AUTO_INCREMENT,
        ID_Cotizacion INT NOT NULL,
        CBM_Total DECIMAL(10,2) NULL,
        Peso_Total DECIMAL(10,2) NULL,
        URL_Proforma TEXT(65535) NULL,
        URL_Packing TEXT(65535) NULL,
        PRIMARY KEY (ID_Proveedor),
        FOREIGN KEY (ID_Cotizacion) REFERENCES carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
        );";
        $tableDetallesProductos="
        CREATE TABLE carga_consolidada_cotizaciones_detalles_producto (
            ID_Producto INT NOT NULL AUTO_INCREMENT,
            ID_Cotizacion INT NOT NULL,
            ID_Proveedor INT NOT NULL,
            URL_Image TEXT(65535) NULL,
            URL_Link TEXT(65535) NULL,
            Nombre_Comercial VARCHAR(500) NULL,
            Uso TEXT(65535) NULL,
            Cantidad DECIMAL(10,2) NULL,
            Valor_unitario DECIMAL(10,2) NULL,
            PRIMARY KEY (ID_Producto),
            FOREIGN KEY (ID_Cotizacion) REFERENCES carga_consolidada_cotizaciones_cabecera(ID_Cotizacion),
            FOREIGN KEY (ID_Proveedor) REFERENCES carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor)
        );";  
        $tableDetallesTributo="CREATE TABLE carga_consolidada_cotizaciones_detalles_tributo (
            ID_Tributo INT NOT NULL AUTO_INCREMENT,
            ID_Tipo_Tributo INT NOT NULL,
            ID_Producto INT NOT NULL,
            ID_Proveedor INT NOT NULL,
            ID_Cotizacion INT NOT NULL,
            Status enum('Pending','Completed') NULL default 'Pending',
            value DECIMAL(10,2) NULL,
            PRIMARY KEY (ID_Tributo),
            FOREIGN KEY (ID_Tipo_Tributo) REFERENCES tipo_carga_consolidada_cotizaciones_tributo(ID_Tipo_Tributo),
            FOREIGN KEY (ID_Producto) REFERENCES carga_consolidada_cotizaciones_detalles_producto(ID_Producto),
            FOREIGN KEY (ID_Proveedor) REFERENCES carga_consolidada_cotizaciones_detalles_proovedor(ID_Proveedor),
            FOREIGN KEY (ID_Cotizacion) REFERENCES carga_consolidada_cotizaciones_cabecera(ID_Cotizacion)
        );";
        DB::statement($tableCabeceraQuery);
        DB::statement($tableTipoTributoTable);
        DB::statement($insertIntoTipoTributo);
        DB::statement($tableDetallesProveedor);
        DB::statement($tableDetallesProductos);
        DB::statement($tableDetallesTributo);
    
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
