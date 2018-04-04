<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomaticJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('subscription_payment_id')->after('user_id')->nullable()->unsigned();
            $table->foreign('subscription_payment_id')->references('id')->on('payments')->onDelete('cascade');
        });

        Schema::create('automatic_jobs', function (Blueprint $table) {
            $table->increments('id');

            //Foreign Key Referencing the id on the users table.
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->integer('subscription_payment_id')->unsigned();
            $table->foreign('subscription_payment_id')->references('id')->on('payments')->onDelete('cascade');

            $table->integer('days_remaining');

            $table->boolean('is_complete')->default(false); //is this subscription cycle complete

            //$table->boolean('is_cancelled')->default(false); //is this subscription cancelled?

            $table->dateTime('begin_date'); //date subscription begins
            $table->dateTime('end_date'); //date subscription ends

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
        Schema::table('orders', function($table) {
            $table->dropForeign(['subscription_payment_id']);
            $table->dropColumn('subscription_payment_id');
        });

        Schema::dropIfExists('automatic_jobs');
    }
}
