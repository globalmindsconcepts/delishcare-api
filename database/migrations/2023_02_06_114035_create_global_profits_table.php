<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_profits', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->references('uuid')->on('users');
            //$table->double('total_point_value');
            $table->double('profit');
            $table->json('data')->nullable();
            //$table->integer('month_count');
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
        Schema::dropIfExists('globa_profits');
    }
}
