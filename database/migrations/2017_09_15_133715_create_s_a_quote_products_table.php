<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSAQuoteProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_a_quote_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quantity');
            $table->decimal('price',10,2)->nullable();
            $table->double('tax',10,2)->nullable();
            $table->string('currency',10)->nullable();
            $table->integer('s_a_product_id')->unsigned()->nullable();
            $table->foreign('s_a_product_id')->references('id')->on('s_a_products')->onDelete('cascade');
            $table->integer('s_a_quote_id')->unsigned()->nullable();
            $table->foreign('s_a_quote_id')->references('id')->on('s_a_quotes')->onDelete('cascade');
            $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('s_a_agreement_products');
    }
}
