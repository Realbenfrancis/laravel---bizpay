<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSAAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_a_agreements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug',255)->index();
            $table->string('merchant_slug',255);
            $table->string('bizpay_order_id',255)->nullable();
            $table->integer('structure'); //  one-off:1,instalments:2
            $table->integer('billing_start'); // 1: agreement date; 2: start period; 3: end period
            $table->integer('billing_period'); //  daily: 1, weekly:2, monthly:3, quarterly: 4; bi-annually:5 ; yearly: 6
            $table->smallInteger('payment_info_required');
            $table->smallInteger('different_first_payment');
            $table->smallInteger('can_cancel'); //0 : No; 1: Yes with x days; 2: Anytime
            $table->integer('cancellation_days')->nullable(); //cancellation_days
            $table->smallInteger('refund_check');
            $table->smallInteger('refund_percent');
            $table->smallInteger('renewal');
            $table->text('terms',255555)->nullable();


            $table->integer('gateway')->nullable();
            $table->integer('agreement_term')->nullable();

            //apply on other schemas
            $table->integer('renewal_period')->nullable(); //  daily: 1, weekly:2, monthly:3, quarterly: 4; bi-annually:5 ; yearly: 6

            $table->integer('instalments');
            $table->integer('instalments_remaining');

            $table->string('first_payment',255)->nullable(); // amount
            $table->string('first_payment_date',255)->nullable(); //date string
            $table->string('recurring_payment',255)->nullable();  //amount
            $table->string('recurring_payment_date',255)->nullable();//date string
            $table->string('currency_code',255)->nullable();

            $table->integer('quote_id')->unsigned()->nullable();
            $table->foreign('quote_id')->references('id')->on('s_a_quotes');
            $table->string('user_id_string',255)->nullable(); // user_id from users
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('merchant_id')->unsigned()->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->smallInteger('status')->default(1);
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
        Schema::dropIfExists('s_a_agreements');
    }
}
