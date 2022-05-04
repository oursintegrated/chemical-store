<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sales_code');
            $table->integer('customer_id');
            $table->string('customer_name');
            $table->string('phone_number');
            $table->string('address');
            $table->string('type');
            $table->integer('due_date')->nullable();
            $table->timestamp('transaction_date');
            $table->decimal('total', 14, 2);
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
        Schema::dropIfExists('sales_headers');
    }
}
