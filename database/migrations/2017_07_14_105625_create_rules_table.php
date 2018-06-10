<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateRulesTable
 */
class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rule', 255);
            $table->string('rule_id', 255)->unique();
            $table->string('check_type', 255);
            $table->string('apply_rule_on', 255);
            $table->text('description', 255555)->nullable();
            $table->string('data_type', 255);
            $table->string('limit1', 255)->nullable();
            $table->string('limit2', 255)->nullable();
            $table->string('limit3', 255)->nullable();
            $table->string('action_on', 255)->nullable();
            $table->string('action_type', 255)->nullable();
            $table->string('action_value', 255)->nullable();
            $table->integer('plan_id')->unsigned()->nullable();
            $table->integer('merchant_id')->unsigned()->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->integer('merchant_id_on')->unsigned()->nullable();
            $table->foreign('merchant_id_on')->references('id')->on('merchants')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('rules');
    }
}
