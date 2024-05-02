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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('mode')->nullable();
            $table->integer('min_age')->default(0);
            $table->integer('max_age')->default(0);
            $table->float('distance', 8, 2)->nullable();
            $table->string('current_lat')->nullable();
            $table->string('current_long')->nullable();
            $table->string('interested_in')->nullable();
            $table->boolean('show_more')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('user_preferences');
    }
};
