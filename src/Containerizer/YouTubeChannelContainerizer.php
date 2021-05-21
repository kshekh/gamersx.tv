<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class YouTubeChannelContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    private $homeRowItem;
    private $youtube;

    public function __construct(HomeRowItem $homeRowItem, $youtube)
    {
        $this->homeRowItem = $homeRowItem;
        $this->youtube = $youtube;
    }

    public function getContainers(): Array
    {
        $homeRowItem = $this->homeRowItem;
        $youtube = $this->youtube;

        $channelId = $homeRowItem->getTopic()['topicId'];

        try {
            $info = $youtube->getChannelInfo($channelId)->getItems();
            $broadcast = $youtube->getLiveChannel($channelId)->getItems();
        } catch (\Exception $e) {
            $this->logger->error("Call to YouTube failed with the message \"".$e->getErrors()[0]['message']."\"");
            return Array();
        }

        $info = $info[0];
        $broadcast = !empty($broadcast) ? $broadcast[0] : NULL;

        // No need for a container if we're not displaying and not online
        if (($homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_NONE) &&
            $broadcast === NULL) {
            return Array();
        }

        $rowName = $homeRowItem->getHomeRow()->getTitle();

        if ($broadcast === NULL) {
            $title = $info->getSnippet()->getTitle();
            if ($info->getSnippet()->getCustomURL() !== NULL) {
                $link = 'https://www.youtube.com/c/'.$info->getSnippet()->getCustomURL();
            } else {
                $link = 'https://www.youtube.com/channel/'.$info->getId();
            }
        } else {
            $title = sprintf("%s - \"%s\"",
                $info->getSnippet()->getTitle(), $broadcast->getSnippet()->getTitle());
            $link = 'https://www.youtube.com/v/'.$broadcast->getId()->getVideoId();
        }


        if (($broadcast === NULL && $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_ART) ||
            $homeRowItem->getShowArt() === TRUE) {
            // Get the sized art link
            $imageInfo = $info->getSnippet()->getThumbnails();
            $imageInfo = $imageInfo->getMedium() ? $imageInfo->getMedium() : $imageInfo->getStandard();

            $image = [
                'url' => $imageInfo->getUrl(),
                'class' => 'profile-pic',
                'width' => $imageInfo->getWidth(),
                'height' => $imageInfo->getHeight(),
            ];
        }

        if ($broadcast !== NULL) {
            $embedData = [
                'video' => $broadcast->getId()->getVideoId(),
                'elementId' => 'embed-'.sha1($title)
            ];
        } else {
            $embedData = NULL;
        }

        $channels = [
            [
                'info' => $info,
                'broadcast' => $broadcast,
                'liveViewerCount' => $broadcast ? $broadcast['viewer_count'] : 0,
                'viewedCount' => $info['view_count'],
                'showOnline' => $broadcast !== NULL,
                'onlineDisplay' => [
                    'title' => $title,
                    'showArt' => $homeRowItem->getShowArt(),
                    'showEmbed' => TRUE,
                ],
                'offlineDisplay' => [
                    'title' => $info->getSnippet()->getTitle(),
                    'showArt' => $homeRowItem->getShowArt() || $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_ART,
                    'showEmbed' => $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_STREAM,
                ],
                'itemType' => $homeRowItem->getItemType(),
                'rowName' => $homeRowItem->getHomeRow()->getTitle(),
                'sortIndex' => $homeRowItem->getSortIndex(),
                'image' => $image ?? NULL,
                'link' => $link,
                'componentName' => 'EmbedContainer',
                'embedName' => 'YouTubeEmbed',
                'embedData' => $embedData,
            ]
        ];

        $this->items = $channels;
        $this->options = $homeRowItem->getSortAndTrimOptions();

        $this->sort();
        $this->trim();

        return $this->items;
    }

}
