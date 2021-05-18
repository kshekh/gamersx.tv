<?php

namespace App\Containerizer;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;

abstract class LiveContainerizer implements ContainerizerInterface
{

    protected $items;
    protected $options;

    abstract public function getContainers(): Array;

    protected function sort(): Array
    {
        if (array_key_exists('itemSortType', $this->options)) {
            $sort = $this->options['itemSortType'];
            if ($sort === HomeRow::SORT_ASC) {
                usort($this->items, [$this, 'sortAscendingPopularity']);
            } elseif ($sort === HomeRow::SORT_DESC) {
                usort($this->items, [$this, 'sortDescendingPopularity']);
            } elseif ($sort === HomeRow::SORT_FIXED) {
                usort($this->items, [$this, 'sortByIndex']);
            }
        }

        return $this->items;

    }

    protected function trim(): Array
    {
        if (array_key_exists('maxContainers', $this->options)) {
            $this->items = array_slice($this->items, 0, $this->options['maxContainers']);
        }

        if (array_key_exists('maxLive', $this->options)) {
            $max = $this->options['maxLive'];
            array_walk($this->items, function(&$value, $i) use ($max) {
                if ($i >= $max) {
                    $value['showOnline'] = FALSE;
                }
            });
        }

        return $this->items;
    }

    protected static function sortDescendingPopularity($first, $second) {
        if ($first['broadcast'] !== NULL) {
            if ($second['broadcast'] === NULL) {
                return -1; // only first is broadcasting
            } else {
                return $second['liveViewerCount'] - $first['liveViewerCount'];
            }
        } else if ($second['broadcast'] !== NULL) {
            return 1; //only second is broadcasting
        } else { // nobody broadcasting
            return $second['viewedCount'] - $first['viewedCount'];
        }
    }

    // Sort with the least popular first
    protected static function sortAscendingPopularity($first, $second) {
        return static::sortDescendingPopularity($second, $first);
    }

    // Sort by the sort Index
    protected static function sortByIndex($first, $second) {
        return $second['sortIndex'] - $first['sortIndex'];
    }

}
