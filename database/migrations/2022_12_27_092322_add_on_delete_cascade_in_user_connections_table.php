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
        Schema::table('user_connections', function (Blueprint $table) {
            $table->dropForeign('user_connections_from_user_id_foreign');
            $table->dropForeign('user_connections_to_user_id_foreign');
            $table->foreign('from_user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('to_user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_connections', function (Blueprint $table) {
            //
        });
    }
};
