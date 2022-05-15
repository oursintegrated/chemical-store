<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sales_header_id');
            $table->foreign('sales_header_id')->references('id')->on('sales_headers')->onDelete('cascade');
            $table->integer('product_id');
            $table->string('product_name');
            $table->integer('qty');
            $table->string('unit')->nullable();
            $table->decimal('price', 14, 2);
            $table->decimal('total', 14, 2);
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
        Schema::dropIfExists('sales_details');
    }
}
