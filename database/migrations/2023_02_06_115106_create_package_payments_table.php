<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_payments', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->unique()->references('uuid')->on('users');
            $table->double('amount');
            $table->double('point_value')->nullable();
            $table->enum('status', ['approved', 'processing', 'declined'])->default('processing');
            $table->string('reference');
            $table->string('processor')->default('paystack');
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
        Schema::dropIfExists('package_payments');
    }
}
