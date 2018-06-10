<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * Status details
     *
     * 1: Created
     * 2: Stripe active
     * 3: Other service
     * 0: Inactive
     *
     * Merchant Category
     *
     * 1:  Dedicated
     * 2:  Shared
     *
     *
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->increments('id');

            $table->string('merchant_id',255);
            $table->string('merchant_name',255);
            $table->string('merchant_logo',255)->nullable();
            $table->string('merchant_website',255)->nullable();
            $table->integer('tax')->nullable();
            $table->string('color',255)->nullable();

            $table->smallInteger('merchant_type')->nullable(); // 1: individual; 2: organisation
            $table->string('phone_number',255)->nullable();
            $table->string('number_of_staff',255)->nullable();
            $table->string('industry',255)->nullable();
            $table->string('organisation_number',255)->nullable();
            $table->string('tax_number',255)->nullable();
            $table->smallInteger('card_check')->nullable();
            $table->smallInteger('dd_check')->nullable();


//            $table->string('merchant_stripe_public_key',255)->nullable();
//            $table->string('merchant_stripe_secret_key',255)->nullable();
            $table->integer('gateway')->default(1);
            $table->integer('merchant_category')->default(2);
            $table->smallInteger('direct_client')->nullable();
            $table->smallInteger('bizpay_credit')->nullable();
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
        Schema::dropIfExists('merchants');
    }
}
