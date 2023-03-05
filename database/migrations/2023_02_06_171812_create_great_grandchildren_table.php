<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreatGrandchildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('great_grandchildren', function (Blueprint $table) {
            $table->id();
            $table->string('great_grandchild_id')->references('uuid')->on('users');
            $table->string('great_grandparent_id')->references('uuid')->on('users');
            $table->string('parent_id')->references('uuid')->on('users');
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
        Schema::dropIfExists('great_grandchildren');
    }
}
