<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParamsForLocalMarketing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->after('primary_language_id')->nullable();
            $table->decimal('longitude', 11, 8)->after('latitude')->nullable();
            $table->date('dob')->after('email')->nullable();
            $table->string('locale')->after('dob')->nullable();
            $table->string('gender')->after('locale')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->after('url')->nullable();
            $table->decimal('longitude', 11, 8)->after('latitude')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->after('is_active')->nullable();
            $table->decimal('longitude', 11, 8)->after('latitude')->nullable();
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
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('dob');
            $table->dropColumn('locale');
            $table->dropColumn('gender');
        });

        Schema::table('orders', function($table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });

        Schema::table('companies', function($table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
