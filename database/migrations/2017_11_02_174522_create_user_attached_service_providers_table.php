<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAttachedServiceProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_attached_service_providers', function (Blueprint $table) {
            $table->increments('id');

            //Foreign Key Referencing the id on the users table.
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            //Foreign Key Referencing the id on the service_providers table.
            $table->integer('provider_id')->unsigned();
            $table->foreign('provider_id')->references('id')->on('service_providers')->onDelete('cascade');

            //In facebook this means "Friends" that a user has....
            //In Instagram this means "Followers" that a user has...
            $table->integer('traffic');

            //this is how much the user's account is worth in credits.
            $table->integer('net_worth');

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
        Schema::dropIfExists('user_attached_service_providers');
    }
}
