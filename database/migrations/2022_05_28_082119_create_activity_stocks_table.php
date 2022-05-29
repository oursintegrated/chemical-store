<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('updated_by');
            $table->decimal('from_qty', 12, 5);
            $table->decimal('to_qty', 12, 5);
            $table->integer('qty');
            $table->string('description');
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
        Schema::dropIfExists('activity_stocks');
    }
}
