<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreatePaymentGatewayCustomerDetailsTable
 */
class CreatePaymentGatewayCustomerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateway_customer_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stripe_card_id')->nullable();
            $table->string('stripe_customer_id')->nullable(); //can be changed to use a single customer id
            $table->string('gc_mandate_id')->nullable();
            $table->string('gc_customer_id')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_exp_month')->nullable();
            $table->string('card_exp_year')->nullable();
            $table->string('card_country')->nullable();
            $table->string('card_last_four')->nullable();  // extend this to support other gateways
            $table->integer('payment_gateway_id')->unsigned()->nullable();
            $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('payment_gateway_customer_details');
    }
}
