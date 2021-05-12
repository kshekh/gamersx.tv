<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\YouTubeApi;

class YouTubeContainerizer
{
    protected $homeRowItem;
    protected $youtube;

    public function __construct(HomeRowItem $homeRowItem, YouTubeApi $youtube)
    {
        $this->homeRowItem = $homeRowItem;
        $this->youtube = $youtube;
    }
}
