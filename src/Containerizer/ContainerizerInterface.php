<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

interface ContainerizerInterface
{
    public function getContainers(): Array;
}
