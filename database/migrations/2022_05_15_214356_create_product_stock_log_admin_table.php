<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStockLogAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_log_admin', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->text('description');
            $table->decimal('from_qty', 14, 5);
            $table->decimal('to_qty', 14, 5);
            $table->decimal('total');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stock_log_admin');
    }
}
