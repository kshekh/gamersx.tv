<?php

namespace App\Containerizer;

class YouTubePopularContainerizer extends YouTubeContainerizer implements ContainerizerInterface
{
    public function getContainers(): Array
    {
        $broadcasts = $this->youtube->getPopularStreams();

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
                'rowName' => $row->getTitle(),
                'showArt' => false,
                'offlineDisplayType' => HomeRowItem::OFFLINE_DISPLAY_NONE,
                'linkType' => HomeRowItem::LINK_TYPE_GAMERSX,
            ];

            $channels[] = $channel;
        }
        return $channels;
    }

}
