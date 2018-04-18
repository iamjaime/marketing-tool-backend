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
        Schema::create('stripe_withdrawals', function (Blueprint $table) {
            $table->increments('id');

            //Foreign Key Referencing the id on the users table.
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('payout_id'); //payout id

            $table->integer('credits_withdrawn');

            $table->integer('amount_paid_out'); //in pennies

            $table->dateTime('arrival_date'); //date the funds are scheduled to arrive

            $table->boolean('automatic');

            $table->string('balance_transaction');

            $table->string('currency');

            $table->string('description');

            $table->string('destination');

            $table->string('failure_balance_transaction')->nullable();
            $table->string('failure_code')->nullable();
            $table->string('failure_message')->nullable();
            $table->boolean('live_mode')->nullable();

            $table->string('method'); //standard, instant

            $table->string('source_type');
            $table->string('statement_descriptor')->nullable();

            $table->string('status');
            $table->string('type'); //bank account or card

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_account_id')->after('stripe_customer_id')->nullable();
        });

        Schema::create('stripe_withdrawal_methods', function (Blueprint $table) {
            $table->increments('id');

            //Foreign Key Referencing the id on the users table.
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('stripe_account_id');
            $table->string('method_type'); //card, bank_account
            $table->string('method_id'); //example : card_3920jjfa0fj, bk_3joifjasiof93
            $table->string('brand')->nullable(); //example : Master Card / Visa etc.
            $table->string('country');
            $table->string('currency');
            $table->string('last4');
            $table->string('cvc_check')->nullable();
            $table->string('exp_month')->nullable();
            $table->string('exp_year')->nullable();


            $table->boolean('is_instant_payout_available')->default(false);
            $table->boolean('is_standard_payout_available')->default(false);

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
        Schema::table('users', function($table) {
            $table->dropColumn('stripe_account_id');
        });

        Schema::dropIfExists('stripe_withdrawals');


        Schema::table('stripe_withdrawal_methods', function($table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('stripe_withdrawal_methods');
    }
}
