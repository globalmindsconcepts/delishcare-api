<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquilibrumBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equilibrum_bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->references('uuid')->on('users');
            $table->double('value');
            $table->integer('num_downlines');
            $table->double('bonus_value');
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
        Schema::dropIfExists('equilibrum_bonuses');
    }
}
