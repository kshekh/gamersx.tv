<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class TwitchStreamerContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    private $homeRowItem;
    private $twitch;

    public function __construct(HomeRowItem $homeRowItem, $twitch)
    {
        $this->homeRowItem = $homeRowItem;
        $this->twitch = $twitch;
    }

    public function getContainers(): Array
    {
        $homeRowItem = $this->homeRowItem;
        $twitch = $this->twitch;

        $streamerId = $homeRowItem->getTopic()['topicId'];
        $info = $twitch->getStreamerInfo($streamerId)->toArray()['data'][0];
        $broadcast = $twitch->getStreamForStreamer($streamerId)->toArray()['data'];
        $broadcast = !empty($broadcast) ? $broadcast[0] : NULL;

        $rowName = $homeRowItem->getHomeRow()->getTitle();

        $title = $broadcast === NULL ? $info['display_name'] : sprintf("%s playing %s for %d viewers",
            $broadcast['user_name'], $broadcast['game_name'], $broadcast['viewer_count']);

        // No need for a container if we're not displaying and not online
        if (($homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_NONE) &&
            $broadcast === NULL) {
            return Array();
        }

        if (($broadcast === NULL && $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_ART) ||
            $homeRowItem->getShowArt() === TRUE) {
            // Get the sized art link
            $imageWidth = $imageHeight = 300;
            $imageUrl = $info['profile_image_url'];
            $imageUrl = str_replace('{height}', $imageHeight, $imageUrl);
            $imageUrl = str_replace('{width}', $imageWidth, $imageUrl);

            $image = [
                'url' => $imageUrl,
                'class' => 'profile-pic',
                'width' => $imageWidth,
                'height' => $imageHeight,
            ];
        }

        if ($broadcast !== NULL || $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_STREAM) {
            $embedData = [
                'channel' => $info['login'],
                'elementId' => 'embed-'.sha1($title)
            ];
        } else {
            $embedData = NULL;
        }


        switch ($homeRowItem->getLinkType()) {
        case HomeRowItem::LINK_TYPE_GAMERSX:
            $link = '/streamer/'.$info['id'];
            break;
        case HomeRowItem::LINK_TYPE_EXTERNAL:
            $link = 'https://www.twitch.tv/'.$info['login'];
            break;
        default:
            $link = '#';
        }


        $channels = [
            [
                'info' => $info,
                'broadcast' => $broadcast,
                'showLive' => $broadcast !== NULL,
                'liveViewerCount' => $broadcast ? $broadcast['viewer_count'] : 0,
                'viewedCount' => $info['view_count'],
                'title' => $title,
                'itemType' => $homeRowItem->getItemType(),
                'rowName' => $rowName,
                'sortIndex' => $homeRowItem->getSortIndex(),
                'image' => $image ?? NULL,
                'showImage' => $image === NULL,
                'link' => $link,
                'componentName' => 'EmbedContainer',
                'embedName' => 'TwitchEmbed',
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
