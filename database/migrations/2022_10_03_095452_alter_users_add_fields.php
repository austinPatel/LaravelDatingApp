<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('mobile')->nullable()->unique()->after('email');
            $table->integer('age')->nullable()->after('mobile');
            $table->dateTime('birthdate')->nullable()->after('mobile');
            $table->integer('ndis_number')->nullable()->unique()->after('birthdate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('mobile');
            $table->dropColumn('age');
            $table->dropColumn('birthdate');
            $table->dropColumn('ndis_number');
        });
    }
};
