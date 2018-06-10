<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeferredChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * order_type
     *
     * 0: one-off
     * 1: Subscription
     * 2: Instalment
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deferred_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 10, 2);

            $table->decimal('tax', 10, 2)->nullable();
            $table->integer('payment_gateway')->default(1);

            $table->string('payment_date', 255); // mw - change to date for mysql
            $table->string('currency_code', 255);
            $table->string('description', 255)->nullable();

            $table->integer('instalments')->nullable();
            $table->integer('instalments_remaining')->nullable();

            $table->string('order_ref',255)->nullable();
            $table->smallInteger('renewal_check')->nullable();

            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

           $table->integer('s_a_agreement_id')->nullable();
//            $table->foreign('s_a_agreement_id')->references('id')->on('s_a_agreements')->onDelete('cascade');

            $table->integer('order_type')->nullable();
            $table->string('duration', 255)->nullable();

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('merchant_id')->unsigned()->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->string('status_text', 255)->nullable();
            $table->smallInteger('test_check')->nullable();
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
        Schema::dropIfExists('deferred_charges');
    }
}
