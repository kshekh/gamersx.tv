<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use Doctrine\Common\Collections\ArrayCollection;

class ContainerSorter
{
    // Sort with the most popular first
    public static function sortDescendingPopularity($first, $second) {
        if ($first['broadcast'] !== NULL) {
            if ($second['broadcast'] === NULL) {
                return -1; // only first is broadcasting
            } else {
                return $second['broadcast']['viewer_count'] - $first['broadcast']['viewer_count'];
            }
        } else if ($second['broadcast'] !== NULL) {
            return 1; //only second is broadcasting
        } else { // nobody broadcasting
            return $second['info']['view_count'] - $first['info']['view_count'];
        }
    }

    // Sort with the least popular first
    public static function sortAscendingPopularity($first, $second) {
        return static::sortDescendingPopularity($second, $first);
    }
}
