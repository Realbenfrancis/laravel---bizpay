<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSAQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_a_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',200);
            $table->string('slug',30)->index();
            $table->integer('views')->default(0);
            $table->integer('purchase_count')->default(0);
            $table->integer('purchase_limit')->nullable();
            $table->integer('validity_type');
            $table->string('validity',255)->nullable(); //mw - change to date in normal sql //
            $table->string('confirmation_url',255)->nullable();
            $table->smallInteger('prepopulate_check')->nullable();
            $table->integer('plan_id')->unsigned()->nullable();
            $table->foreign('plan_id')->references('id')->on('s_a_plans')->onDelete('cascade');
            $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('s_a_quotes');
    }
}
