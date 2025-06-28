<?php

namespace App\Traits;

use Filament\Support\Colors\Color;

trait ChartAnimationTrait
{

    public static function animate(){

        return  [
            random_int(1, 20), 
            random_int(1, 20), 
            random_int(15, 40), 
            random_int(1, 20), 
            random_int(5, 30), 
            random_int(1, 20), 
            random_int(1, 20)
        ];
    }

    public static function color(){
           $chartColors = [Color::Rose,Color::Gray,Color::Blue,Color::Indigo,Color::Emerald,Color::Orange,Color::Fuchsia];
         
           $colorIndex = random_int(0, count($chartColors)-1);
        return  $chartColors[$colorIndex];
    }

    
    
}
