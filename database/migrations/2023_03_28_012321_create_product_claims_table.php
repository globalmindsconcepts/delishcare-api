<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_claims', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->references('uuid')->on('users');
            $table->integer('product_id')->references('id')->on('products');
            $table->double('points')->default(0);
            $table->enum('status', ['approved', 'processing', 'declined'])->default('processing');
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
        Schema::dropIfExists('product_claims');
    }
}
