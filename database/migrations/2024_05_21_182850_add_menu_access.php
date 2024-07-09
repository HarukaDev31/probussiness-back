<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $querys="INSERT INTO menu
        // (ID_Menu, ID_Padre, Nu_Orden, No_Menu, No_Menu_Url, No_Class_Controller, Txt_Css_Icons, Nu_Separador, Nu_Seguridad, Nu_Activo, Nu_Tipo_Sistema, Txt_Url_Video)
        // VALUES(188, 184, 3, 'Cotizaciones', 'CargaConsolidada/CCotizaciones/listar', 'CCotizaciones', 'fas fa-money-check-alt', 0, 0, 0, 0, NULL);
        
        // INSERT INTO menu_acceso
        // (ID_Empresa, ID_Menu_Grupo_Usuario, ID_Menu, ID_Grupo_Usuario, Nu_Consultar, Nu_Agregar, Nu_Editar, Nu_Eliminar)
        // VALUES(1, 149324, 188, 1, 1, 1, 1, 1);
        // ";
        // DB::unprepared($querys);
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
