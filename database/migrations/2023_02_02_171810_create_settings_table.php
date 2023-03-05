<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->double('unit_point_value')->nullable();
            $table->double('welcome_bonus_percentage')->nullable(); //2% 
            $table->double('incentive_percentage')->nullable(); //5% 

            $table->double('equillibrum_bonus')->nullable(); //user earns when he has 2 direct downlines
            $table->double('loyalty_bonus_percentage')->nullable();

            $table->double('profit_pool_percentage')->nullable(); //2% 
            $table->integer('profit_pool_duration')->nullable(); // 6 months
            $table->integer('profit_pool_days_offset')->nullable(); //after 30 days
            $table->integer('profit_pool_num_of_downlines')->nullable(); //number of legs require to qulify for profit pool bonus
            $table->json('profit_pool_packages')->nullable(); //packages qualified for profit pool

            $table->double('minimum_withdrawal')->nullable();
            $table->double('maximum_withdrawal')->nullable();

            $table->double('global_profit_first_percentage')->nullable();
            $table->double('global_profit_second_percentage')->nullable();

            $table->integer('next_global_profit_share_month')->nullable();
            $table->integer('next_global_profit_share_day')->nullable();

            $table->double('placement_bonus_percentage')->default(0);

            //$table->
            
            //$table->double('')
            
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
        Schema::dropIfExists('settings');
    }
}
