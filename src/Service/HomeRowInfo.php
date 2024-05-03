<?php

namespace App\Service;

use Exception;

class HomeRowInfo
{
    public function convertHoursMinutesToSeconds($time)
    {
        // Split the string at the space (handles date portion)
          $time_parts = explode(' ', $time);

          // Get the last part (assuming it's HH:MM)
          $time_string = end($time_parts);

          $array = explode(':', $time_string);

          try {
            if (count($array) >= 2 && is_numeric($array[0]) && is_numeric($array[1])) {
              // Only proceed if there are 2 parts and both are numeric
              return $array[0] * 3600 + $array[1] * 60;
            } else {
              // Handle invalid format or missing minutes
              return null;  // Or throw an exception if desired
            }
          } catch (Exception $exception) {
            dd($exception->getMessage());  // For debugging
          }
    }
}
