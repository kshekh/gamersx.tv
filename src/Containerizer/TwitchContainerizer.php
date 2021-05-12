<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\TwitchApi;

class TwitchContainerizer
{
    protected $homeRowItem;
    protected $twitch;

    public function __construct(HomeRowItem $homeRowItem, TwitchApi $twitch)
    {
        $this->homeRowItem = $homeRowItem;
        $this->twitch = $twitch;
    }

}
