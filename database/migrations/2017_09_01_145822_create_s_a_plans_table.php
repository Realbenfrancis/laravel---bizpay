<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSAPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_a_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('plan',200);
            $table->integer('views')->default(0);
            $table->integer('purchase_count')->default(0);
            $table->string('slug',30)->index();
            $table->integer('structure'); //  one-off:1,instalments:2
            $table->integer('billing_start'); // 1: agreement date; 2: start period; 3: end period
            $table->integer('billing_period'); //  daily: 1, weekly:2, monthly:3, quarterly: 4; bi-annually:5 ; yearly: 6
            $table->smallInteger('payment_info_required');
            $table->smallInteger('different_first_payment');
            $table->integer('first_payment')->nullable(); // percentage
            $table->integer('first_payment_date')->nullable(); //days
            $table->smallInteger('can_cancel'); //0 : No; 1: Yes with x days; 2: Anytime
            $table->integer('cancellation_days')->nullable(); //cancellation_days
            $table->smallInteger('refund_check');
            $table->smallInteger('refund_percent');
            $table->smallInteger('renewal');

            $table->integer('agreement_term')->nullable();

            $table->text('terms',255555)->nullable();
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
        Schema::dropIfExists('s_a_plans');
    }
}
