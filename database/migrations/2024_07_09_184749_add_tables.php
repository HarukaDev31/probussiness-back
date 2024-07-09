<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AddTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query="CREATE TABLE suppliers (
            id_supplier INT NOT NULL,
            name VARCHAR(200) NULL,
            phone VARCHAR(50) NULL,
            code VARCHAR(10) NULL,
            rating DECIMAL(10,2) NULL,
            c_orders INT NOT NULL
        )
        ";
        DB::statement($query);
        //
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
