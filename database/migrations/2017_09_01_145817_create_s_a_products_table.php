<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSAProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_a_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->string('slug',30)->index();
            $table->string('product_id',255)->nullable();
            $table->integer('views')->default(0);
            $table->integer('purchase_count')->default(0);
            $table->integer('quantity')->nullable();
            $table->text('description',2555555)->nullable();
            $table->string('tags',2555)->nullable();
            $table->string('currency',10);
            $table->decimal('price',10,2);
            $table->double('tax',10,2)->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('merchant_id')->unsigned()->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->smallInteger('status');
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
        Schema::dropIfExists('s_a_products');
    }
}
