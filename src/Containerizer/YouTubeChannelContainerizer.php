<?php

namespace App\Containerizer;

class YouTubeChannelContainerizer implements ContainerizerInterface
{
    public static function getContainers($homeRowItem, $youtube): Array
    {
        $options = $homeRowItem->getContainerizerOptions();
        $channelId = $options['topic']['topicId'];

        $info = $youtube->getChannelInfo($channelId)->getItems();
        $broadcast = $youtube->getLiveChannel($channelId)->getItems();

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
                'rowType' => $homeRowItem->getItemType(),
                'rowName' => $homeRowItem->getHomeRow()->getTitle(),
                'sortIndex' => $homeRowItem->getSortIndex(),
                'showArt' => $homeRowItem->getShowArt(),
                'offlineDisplayType' => $homeRowItem->getOfflineDisplayType(),
                'linkType' => $homeRowItem->getLinkType(),
            ]
        ];
    }

}
