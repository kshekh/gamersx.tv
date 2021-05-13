<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class YouTubePopularContainerizer implements ContainerizerInterface
{
    public static function getContainers($homeRowItem, $youtube): Array
    {
        $broadcasts = $youtube->getPopularStreams();

        $channels = Array();

        foreach ($broadcasts->getItems() as $i => $broadcast) {
            $snippet = $broadcast->getSnippet();
            $channelInfo = [
                'id' => $broadcast->getId(),
                'title' => $snippet->getTitle(),
                'channelName' => $snippet->getChannelTitle(),
                'thumbnails' => $snippet->getThumbnails()->getStandard(),
            ];

            $channel = [
                'rowType' => 'youtube',
                'broadcast' => $channelInfo,
                'sortIndex' => $i,
                'rowName' => 'Popular YouTubers',
                'showArt' => false,
                'offlineDisplayType' => HomeRowItem::OFFLINE_DISPLAY_NONE,
                'linkType' => HomeRowItem::LINK_TYPE_GAMERSX,
            ];

            $channels[] = $channel;
        }
        return $channels;
    }

}
