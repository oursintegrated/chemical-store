<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpnameStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opname_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->date('opname_date');
            $table->decimal('stock', 14, 2);
            $table->integer('updated_by');
            $table->$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opname_stocks');
    }
}
