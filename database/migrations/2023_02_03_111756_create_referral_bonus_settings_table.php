<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralBonusSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_bonus_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('package_id')->references('id')->on('packages');
            $table->double('generation_1_percentage')->default(0);
            $table->double('generation_2_percentage')->default(0);
            $table->double('generation_3_percentage')->default(0);
            $table->double('generation_4_percentage')->default(0);
            $table->double('generation_5_percentage')->default(0);
            $table->double('generation_6_percentage')->default(0);
            
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
        Schema::dropIfExists('referral_bonus_settings');
    }
}
