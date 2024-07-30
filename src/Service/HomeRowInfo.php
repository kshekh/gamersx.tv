<?php

namespace App\Service;

class HomeRowInfo
{
    public function convertHoursMinutesToSeconds($time)
    {
        $array = explode(':', $time);
        if(isset($array[1])){
            return $array[0] * 3600 + $array[1] * 60;
        }else{
            return $time;
        }
    }
}
