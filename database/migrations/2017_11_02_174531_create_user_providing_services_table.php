<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProvidingServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_providing_services', function (Blueprint $table) {
            $table->increments('id');

            //Foreign Key Referencing the id on the orders table.
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            //Foreign Key Referencing the id on the users table.
            $table->integer('providing_service_user_id')->unsigned();
            $table->foreign('providing_service_user_id')->references('id')->on('users')->onDelete('cascade');

            //Foreign Key Referencing the id on the users table.
            $table->integer('buying_service_user_id')->unsigned();
            $table->foreign('buying_service_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->boolean('is_confirmed')->default(false);

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
        Schema::dropIfExists('user_providing_services');
    }
}
