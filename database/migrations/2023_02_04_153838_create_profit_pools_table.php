<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfitPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profit_pools', function (Blueprint $table) {
            $table->id();
            //$table->integer('package'); 
            $table->string('user_uuid')->references('uuid')->on('users');
            $table->string('value'); // 2% of reg. PV x unit pv 
            $table->integer('month_count')->nullable();
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
        Schema::dropIfExists('profit_pools');
    }
}
