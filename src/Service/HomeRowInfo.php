<?php

namespace App\Service;

class HomeRowInfo
{
    public function convertHoursMinutesToSeconds($time)
    {
        $array = explode(':', $time);
        return $array[0] * 3600 + $array[1] * 60;
    }
}
