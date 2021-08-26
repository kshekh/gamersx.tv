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

        $info = $twitch->getStreamerInfo($streamerId);
        if (200 !== $info->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$info->getStatusCode());
            unset($info);
            return Array();
        }

        $broadcast = $twitch->getStreamForStreamer($streamerId);
        if (200 !== $broadcast->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$broadcast->getStatusCode());
            unset($broadcast);
            return Array();
        }

        $info = $info->toArray()['data'][0];
        $broadcast = $broadcast->toArray()['data'];
        $broadcast = !empty($broadcast) ? $broadcast[0] : NULL;

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
                'elementId' => uniqid('embed-'),
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
        case HomeRowItem::LINK_TYPE_CUSTOM:
            $link = $homeRowItem->getCustomLink();
            break;
        default:
            $link = '#';
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
                    'showOverlay' => TRUE,
                ],
                'offlineDisplay' => [
                    'title' => $info['login'],
                    'showArt' => $homeRowItem->getShowArt() || $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_ART,
                    'showEmbed' => $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_STREAM,
                    'showOverlay' => $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_OVERLAY,
                ],
                'itemType' => $homeRowItem->getItemType(),
                'rowName' => $homeRowItem->getHomeRow()->getTitle(),
                'sortIndex' => $homeRowItem->getSortIndex(),
                'image' => $image ?? NULL,
                'overlay' => $this->uploader->asset($homeRowItem, 'overlayArtFile'),
                'customArt' => $this->uploader->asset($homeRowItem, 'customArtFile'),
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
