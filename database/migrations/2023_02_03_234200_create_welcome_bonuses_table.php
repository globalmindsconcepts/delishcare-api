<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWelcomeBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('welcome_bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->unique();
            //$table->integer('package'); //user registration package
            //$table->double('reg_point_value'); //user registration point value
            $table->double('bonus'); // welcome bonus calculation 2% of reg_point_value x unit_pv
            $table->json('data')->nullable();
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
        Schema::dropIfExists('welcome_bonuses');
    }
}
