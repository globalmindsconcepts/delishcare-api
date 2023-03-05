<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('txn_reference'); 
            $table->string('user_uuid');
            $table->decimal('amount',10,2);
            $table->decimal('fee',10,2)->default(0);
            $table->string('source_reference')->nullable();
            $table->string('processor')->nullable();
            $table->enum('txn_type',['credit','debit']);
            $table->enum('txn_source',['package_payment','payout']);
            $table->enum('txn_status',['processing','successful','failed'])->default('processing');
            $table->text('narration');
            $table->string('currency')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
