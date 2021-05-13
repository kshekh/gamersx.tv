<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

interface ContainerizerInterface
{
    public static function getContainers(HomeRowItem $homeRowItem, $service): Array;
}
