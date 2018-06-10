<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * user_type
     *
     * 0: Admin
     * 1: Merchant Admin
     * 2: Merchant Manager
     * 3: Client
     *
     *
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('address_line1', 255)->nullable();
            $table->string('address_line2', 255)->nullable();
            $table->string('address_line3', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('postcode', 255)->nullable();
            $table->string('country', 255)->nullable();
            $table->string('user_id', 255)->index()->nullable();
            $table->string('user_category', 255)->nullable();
            $table->string('organisation_name', 255)->nullable();
            $table->string('company_no', 255)->nullable();
            $table->string('role', 255)->nullable();

            $table->string('phone_number', 255)->nullable();
            $table->string('job_title', 255)->nullable();

            $table->string('email')->nullable();
            $table->string('api_token', 60)->index()->unique()->nullable();

            $table->bigInteger('api_limit')->nullable();
            $table->bigInteger('api_usage')->nullable();



            $table->string('password');
            $table->integer('user_type');
            $table->boolean('confirmed')->default(0);
            $table->string('confirmation_code')->nullable();
            $table->integer('merchant_id')->unsigned()->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->smallInteger('status');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
