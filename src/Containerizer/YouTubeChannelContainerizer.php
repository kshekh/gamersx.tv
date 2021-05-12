<?php

namespace App\Containerizer;

class YouTubeChannelContainerizer extends YouTubeContainerizer implements ContainerizerInterface
{
    public function getContainers(): Array
    {
        $channelId = $this->homeRowItem->getContainerizerOptions()['filter']['twitchId'];
        $info = $this->youtube->getChannelInfo($channelId)->getItems();
        $broadcast = $this->youtube->getLiveChannel($channelId)->getItems();

        if (!empty($broadcast)) {
            $broadcast = $broadcast[0];
            $snippet = $broadcast->getSnippet();
            $channelInfo = [
                'id' => $broadcast->getId()->getVideoId(),
                'title' => $snippet->getTitle(),
                'channelName' => $snippet->getChannelTitle(),
                'thumbnails' => $snippet->getThumbnails()->getDefault(),
            ];
        } else {
            $channelInfo = NULL;
        }

        return [
            [
                'info' => $info,
                'broadcast' => $channelInfo,
                'rowType' => $this->homeRowItem->getItemType(),
                'rowName' => $this->homeRowItem->getHomeRow()->getTitle(),
                'sortIndex' => $this->homeRowItem->getSortIndex(),
                'showArt' => $this->homeRowItem->getShowArt(),
                'offlineDisplayType' => $this->homeRowItem->getOfflineDisplayType(),
                'linkType' => $this->homeRowItem->getLinkType(),
            ]
        ];
    }

}
