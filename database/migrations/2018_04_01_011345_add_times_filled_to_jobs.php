<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimesFilledToJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('fill_times')->after('quantity')->default(1);
            $table->string('language')->after('fill_times')->default('en'); //The locale to target ( es or en )
        });

        Schema::table('user_providing_services', function (Blueprint $table) {

            //Foreign Key Referencing the id on the users table.
            //This is for the user that is providing the service
            $table->integer('providing_user_id')->after('order_id')->nullable()->unsigned();
            $table->foreign('providing_user_id')->references('id')->on('users')->onDelete('cascade');

            //the amount of times remaining for the order to be considered filled
            $table->integer('fills_remaining')->after('buying_service_user_id')->default(0);

            //The language settings for the user providing the service.
            $table->string('language')->after('fills_remaining')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_providing_services', function (Blueprint $table){
            $table->dropForeign(['providing_user_id']);
            $table->dropColumn('providing_user_id');
            $table->dropColumn('language');
        });

        Schema::table('orders', function($table) {
            $table->dropColumn('fill_times');
            $table->dropColumn('language');
        });
    }
}
