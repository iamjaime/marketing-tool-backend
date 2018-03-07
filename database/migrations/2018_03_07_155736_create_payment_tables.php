<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {

            $table->increments('id');

            //Foreign Key Referencing the id on the users table.
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('stripe_card_id')->nullable();
            $table->string('last4')->nullable();
            $table->string('exp_month')->nullable();
            $table->string('exp_year')->nullable();
            $table->string('country')->nullable();
            $table->string('brand')->nullable();
            $table->string('stripe_charge_token')->nullable();

            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {

            $table->increments('id');

            //Foreign Key Referencing the id on the users table.
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('charge_id');

            $table->integer('amount');

            $table->string('plan');

            $table->string('status');

            $table->longText('meta_data'); //serialized response from merchant


            $table->integer('card_id')->unsigned();
            $table->foreign('card_id')->references('id')->on('payment_methods')->onDelete('cascade');


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
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
    }
}
