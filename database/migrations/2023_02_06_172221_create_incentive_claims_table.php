<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncentiveClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incentive_claims', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->references('uuid')->on('users');
            $table->integer('incentive_id')->references('id')->on('incentives');
            $table->enum('status', ['approved', 'processing', 'declined']);
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
        Schema::dropIfExists('incentive_claims');
    }
}
