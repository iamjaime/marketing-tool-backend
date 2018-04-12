<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->increments('id');

            //Foreign Key Referencing the id on the users table.
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('credits_withdrawn');
            $table->integer('amount_paid_out'); //in pennies

            $table->integer('transaction_fee'); //in pennies

            $table->string('method'); //stripe, paypal, smi

            $table->string('status');

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_account_id')->after('stripe_customer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('stripe_account_id');
        });
        Schema::dropIfExists('withdrawals');
    }
}
