<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_products', function (Blueprint $table) {
            $table->increments('id');

            $table->text('details',2555555555)->nullable();
            $table->decimal('price',10,2)->nullable();
            $table->decimal('tax',10,2)->nullable();
            $table->string('currency',10)->nullable();

            $table->integer('s_a_agreement_id')->unsigned()->nullable();
            $table->foreign('s_a_agreement_id')->references('id')->on('s_a_agreements')->onDelete('cascade');


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
        Schema::dropIfExists('dynamic_products');
    }
}
