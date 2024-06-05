<?php

namespace App\Service;

use DateTime;
use Exception;

class HomeRowInfo
{
    /**
     * @throws Exception
     */
    public function convertHoursMinutesToSeconds($dateTime): int | null
    {
        $time = $this->getTime($dateTime);
        $array = explode(':', $time);
        if (!isset($array[0])) {dd($array);}
        try {
            if (count($array) >= 2 && is_numeric($array[0]) && is_numeric($array[1])) {
              // Only proceed if there are 2 parts and both are numeric
              return (int)($array[0] * 3600 + $array[1] * 60);
            } else {
              // Handle invalid format or missing minutes
              return null;  // Or throw an exception if desired
            }
        } catch (Exception $exception) {
            dd($exception->getMessage());  // For debugging
        }
    }

    /**
     * @throws Exception
     */
    protected function getTime(mixed $dateTime): string
    {
        if ($dateTime === '0000-00-00 00:00:00') {
            return '00:00';
        }

        return !($dateTime instanceof DateTime) ?  (new DateTime($dateTime))->format('H:i') : $dateTime->format('H:i');
    }
}
