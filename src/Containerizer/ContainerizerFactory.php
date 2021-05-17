<?php

namespace App\Containerizer;

use App\Entity\HomeRow;
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

    public function __invoke($toBeContainerized): ContainerizerInterface
    {
        if ($toBeContainerized instanceof HomeRowItem) {
            switch($toBeContainerized->getItemType()) {
            case HomeRowItem::TYPE_GAME:
                return new TwitchGameContainerizer($toBeContainerized, $this->twitch);
            case HomeRowItem::TYPE_STREAMER:
                return new TwitchStreamerContainerizer($toBeContainerized, $this->twitch);
            case HomeRowItem::TYPE_CHANNEL:
                return new YouTubeChannelContainerizer($toBeContainerized, $this->youtube);
            case HomeRowItem::TYPE_YOUTUBE:
                return new YouTubePopularContainerizer($toBeContainerized, $this->youtube);
            default:
                break;
            }
        } elseif ($toBeContainerized instanceof HomeRow) {
            return new HomeRowContainerizer($toBeContainerized, $this);
        }
    }


}
