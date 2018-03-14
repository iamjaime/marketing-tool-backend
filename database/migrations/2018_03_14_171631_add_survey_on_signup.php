<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSurveyOnSignup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //Foreign Key Referencing the id on the users table.
            $table->integer('referred_by')->after('stripe_customer_id')->nullable()->unsigned();
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('cascade');

            $table->string('heard_about_smi')->after('referred_by')->nullable();
            //where did you hear about smi?
            // Facebook, Twitter, Google, YouTube, Other

            $table->string('interested_in')->after('heard_about_smi')->nullable();

            //Checks if referral commission was received
            $table->boolean('referral_commission_received')->after('interested_in')->default(false);

            //Interested in......
            // ["working with smi", "investing in smi", "using the SMI platform for publicity"]

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
            $table->dropForeign(['referred_by']);
            $table->dropColumn('referred_by');
            $table->dropColumn('heard_about_smi');
            $table->dropColumn('interested_in');
            $table->dropColumn('referral_commission_received');
        });
    }
}
