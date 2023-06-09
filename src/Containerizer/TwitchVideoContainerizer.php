<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;

class TwitchVideoContainerizer extends LiveContainerizer implements ContainerizerInterface
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
        $homeRowInfo = new HomeRowInfo();
        $homeRowItem = $this->homeRowItem;
        $twitch = $this->twitch;

        $separatedUrl = explode('/', parse_url($homeRowItem->getVideoId(), PHP_URL_PATH));

        if ($separatedUrl[1] == 'videos') {
            $videoId = $separatedUrl[2];
            $embedUrl = 'https://player.twitch.tv/?parent=gamersx.tv&video='.$videoId;
        } else {
            $videoId = $separatedUrl[3];
            $embedUrl = 'https://clips.twitch.tv/embed?clip='.$videoId.'&parent=gamersx.tv';
        }

        try {
            $info = $twitch->getVideoInfo($videoId)->toArray();
        } catch (\Exception $e) {
            $this->logger->error("Call to twitch failed with the message \"".$e->getErrors()[0]['message']."\"");
            return Array();
        }

        $info = $info['data'][0];
        $broadcast = null;
        $description = $homeRowItem->getDescription();
        $currentTime = $homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));

        $isPublished = $homeRowItem->getIsPublished();

        if (!$isPublished) {
            return Array();
        }

        $isPublishedStartTime = $homeRowItem->getIsPublishedStart();
        $isPublishedEndTime = $homeRowItem->getIsPublishedEnd();

        if (
            !is_null($isPublishedStartTime) && !is_null($isPublishedEndTime) &&
            (($currentTime >= $isPublishedStartTime) && ($currentTime <= $isPublishedEndTime))
        ) {
            // No need for a container if we're not displaying and not online
            if (($homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_NONE) &&
                $broadcast === NULL) {
                return Array();
            }

            $title = $info['title'];
            $link = $videoId;

            if ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_GAMERSX) {
                $link = '/channel/'.$info['id'];
            } elseif ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_CUSTOM) {
                $link = $homeRowItem->getCustomLink();
            }

            if (($broadcast === NULL && $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_ART) ||
                $homeRowItem->getShowArt() === TRUE) {
                // Get the sized art link
                $imageUrl = $info['thumbnail_url'];
                $imageUrl = $imageUrl;
                $imageWidth = '200';
                $imageHeight = '200';

                $image = [
                    'url' => $imageUrl,
                    'class' => 'profile-pic',
                    'width' => $imageWidth,
                    'height' => $imageHeight,
                ];
            }

            $embedData = [
                'video' => $videoId,
                'elementId' => uniqid('embed-'),
                'url' => $embedUrl,
            ];

            if ($info !== NULL) {
                if ($info['view_count'] !== NULL) {
                    $info['statistics_view_count'] = $info['view_count'];
                }
            }

            $channels = [
                [
                    'info' => $info,
                    'broadcast' => $broadcast,
                    'liveViewerCount' => $broadcast ? $broadcast['viewer_count'] : 0,
                    'viewedCount' => isset($info['statistics_view_count']) ? (int) $info['statistics_view_count'] : 0,
                    'showOnline' => true, // If it true then it's show video otherwise it show Overlay
                    'onlineDisplay' => [
                        'title' => $title,
                        'showArt' => $homeRowItem->getShowArt(),
                        'showEmbed' => TRUE,
                        'showOverlay' => TRUE,
                    ],
                    'offlineDisplay' => [
                        'title' => $info['title'],
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
                    'description' => $description
                ]
            ];

            $this->items = $channels;
            $this->options = $homeRowItem->getSortAndTrimOptions();

            $this->sort();
            $this->trim();

            return $this->items;
        }

        return Array();
    }
}
