<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {

            $table->increments('id');

            $table->boolean('is_active')->default(0); //if we see something wrong, we can de-activate the company

            $table->string('company_name');
            $table->string('company_email');

            $table->string('company_phone')->nullable();
            $table->string('company_logo')->nullable();

            $table->text('company_description'); //short description of what the company does.

            $table->string('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_province')->nullable();
            $table->string('company_postal_code')->nullable();
            $table->string('company_country')->nullable();

            $table->string('url')->nullable();

            //Foreign Key Referencing the id on the users table.
            $table->integer('primary_language_id')->unsigned();
            $table->foreign('primary_language_id')->references('id')->on('languages')->onDelete('cascade');

            $table->text('interested_in'); //what is the company interested in advertising on here. (youtube video, facebook page, website, etc.)
            $table->text('interested_in_service_providers')->nullable(); //Facebook, twitter, instagram etc.

            $table->text('budget_for_marketing')->nullable(); //budget for marketing
            $table->text('budget_for_marketing_frequency')->nullable(); //daily, weekly, monthly etc.

            //Engagement bonus for SMI workers ( are you willing to pay a bonus to our workers for helping to convert sales? )
            $table->boolean('engagement_bonus');
            $table->integer('engagement_bonus_in_smi_credits_per_sale')->nullable(); //How many SMI are you willing to pay SMI workers as a bonus for converting sales?


            $table->dateTime('when_do_you_want_to_begin'); //when do they want to begin marking with us?


            $table->timestamps();
        });

        Schema::create('user_companies', function (Blueprint $table) {

            $table->increments('id');

            //Foreign Key Referencing the id on the users table.
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            //Foreign Key Referencing the id on the users table.
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            //Foreign Key Referencing the id on the users table.
            $table->integer('company_id')->after('user_id')->nullable()->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
        Schema::dropIfExists('user_companies');
        Schema::dropIfExists('companies');
    }
}
