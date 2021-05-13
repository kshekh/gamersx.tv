<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\TwitchApi;
use App\Service\YouTubeApi;

class ContainerizerFactory
{
    private $twitch;
    private $youtube;
    private $containerizers;

    public function __construct(TwitchApi $twitch, YouTubeApi $youtube, iterable $containerizers)
    {
        $this->twitch = $twitch;
        $this->youtube = $youtube;
        $this->containerizers = $containerizers;
    }

    public function __invoke($homeRowItem): array
    {
        switch($homeRowItem->getItemType()) {
        case HomeRowItem::TYPE_GAME:
            return TwitchGameContainerizer::getContainers($homeRowItem, $this->twitch);
        case HomeRowItem::TYPE_STREAMER:
            return TwitchStreamerContainerizer::getContainers($homeRowItem, $this->twitch);
        case HomeRowItem::TYPE_CHANNEL:
            return YouTubeChannelContainerizer::getContainers($homeRowItem, $this->youtube);
        case HomeRowItem::TYPE_YOUTUBE:
            return YouTubePopularContainerizer::getContainers($homeRowItem, $this->youtube);
        default:
            return Array();
        }
    }


}
