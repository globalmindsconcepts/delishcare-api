<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rank;

class RankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Rank::first()){
            (new Rank([
                'name' => 'PAND',
                'points' => 600,
            ]))->save();
    
            (new Rank([
                'name' => 'ROOKIE',
                'points' => 1200,
            ]))->save();
    
            (new Rank([
                'name' => 'BISHOP',
                'points' => 3600,
            ]))->save();
    
            (new Rank([
                'name' => 'Leader',
                'points' => 14400,
            ]))->save();
    
            (new Rank([
                'name' => 'MASTER',
                'points' => 32400,
            ]))->save();
    
            (new Rank([
                'name' => 'GRAND MASTER',
                'points' => 97200,
            ]))->save();
    
            (new Rank([
                'name' => 'ALPHA',
                'points' => 388400,
            ]))->save();
        }
    }
}
