<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class YouTubeChannelContainerizer implements ContainerizerInterface
{
    public static function getContainers($homeRowItem, $youtube): Array
    {
        $options = $homeRowItem->getContainerizerOptions();
        $channelId = $options['topic']['topicId'];

        $info = $youtube->getChannelInfo($channelId)->getItems();
        $info = $info[0];

        $broadcast = $youtube->getLiveChannel($channelId)->getItems();
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

        if ($broadcast !== NULL || $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_STREAM) {
            $embedData = [
                'video' => $broadcast->getId()->getVideoId(),
                'elementId' => 'embed-'.sha1($title)
            ];
        } else {
            $embedData = NULL;
        }

        return [
            [
                'info' => $info,
                'broadcast' => $broadcast,
                'title' => $title,
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
    }

}
