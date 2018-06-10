<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * stripe: 1
     * gocardless: 2
     * worldpay: 3
     * paypal: 4
     *
     * Stripe:
     * credential_1:  stripe_public_key
     * credential_2:  stripe_secret_key
     *
     * Gocardless
     * credential_1: access_token
     */
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gateway');
            $table->string('credential_1', 255)->nullable();
            $table->string('credential_2', 255)->nullable();
            $table->string('credential_3', 255)->nullable();
            $table->string('credential_4', 255)->nullable();
            $table->decimal('additional_charge', 10,2)->nullable();
            $table->smallInteger('additional_charge_single_price')->nullable();
            $table->string('account_type', 255)->nullable(); // stripe:1, stripe_connect:2, gocardless basic:1, gocardless plus:2, gocardless pro: 3
            $table->integer('merchant_id')->unsigned()->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->text('other_info', 2555555)->nullable();
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
        Schema::dropIfExists('payment_gateways');
    }
}
