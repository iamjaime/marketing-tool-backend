<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //Working with SMI questions
            $table->integer('earning_goal_amount')->after('interested_in')->nullable();
            $table->string('earning_currency')->after('earning_goal_amount')->nullable();
            $table->string('earning_frequency')->after('earning_currency')->nullable();
            $table->integer('daily_working_frequency')->after('earning_frequency')->nullable();

            //Investing in SMI questions
            $table->integer('possible_investment_amount')->after('daily_working_frequency')->nullable();
            $table->string('investment_currency')->after('possible_investment_amount')->nullable();

            //Publicity with SMI questions
            $table->integer('publicity_amount_spent')->after('investment_currency')->nullable();
            $table->string('publicity_currency')->after('publicity_amount_spent')->nullable();
            $table->string('publicity_frequency')->after('publicity_currency')->nullable();
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
            $table->dropColumn('earning_goal_amount');
            $table->dropColumn('earning_currency');
            $table->dropColumn('earning_frequency');
            $table->dropColumn('daily_working_frequency');
            $table->dropColumn('possible_investment_amount');
            $table->dropColumn('investment_currency');
            $table->dropColumn('publicity_amount_spent');
            $table->dropColumn('publicity_currency');
            $table->dropColumn('publicity_frequency');
        });
    }
}
