<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\TwitchApi;
use App\Service\YouTubeApi;

class  ContainerizerFactory
{
    private $twitch;
    private $youtube;

    public function __construct(TwitchApi $twitch, YouTubeApi $youtube)
    {
        $this->twitch = $twitch;
        $this->youtube = $youtube;
    }

    public function __invoke($homeRowItem): ContainerizerInterface
    {
        switch($homeRowItem->getItemType()) {
        case HomeRowItem::TYPE_GAME:
            return new TwitchGameContainerizer($homeRowItem, $this->twitch);
        case HomeRowItem::TYPE_STREAMER:
            return new TwitchStreamerContainerizer($homeRowItem, $this->twitch);
        case HomeRowItem::TYPE_CHANNEL:
            return new YouTubeChannelContainerizer($homeRowItem, $this->youtube);
        case HomeRowItem::TYPE_POPULAR:
            return new TwitchGameContainerizer($homeRowItem, $this->twitch);
        case HomeRowItem::TYPE_YOUTUBE:
            return new YouTubePopularContainerizer($homeRowItem, $this->twitch);
        default:
            return NULL;
        }
    }


}
