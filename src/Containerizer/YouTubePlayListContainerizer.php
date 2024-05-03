<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;

class YouTubePlayListContainerizer extends LiveContainerizer implements ContainerizerInterface
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
        return Array();
        //Following code not needed as playlist are not showing in front-end+video already extracted from admin
        $homeRowInfo = new HomeRowInfo();
        $homeRowItem = $this->homeRowItem;
        $youtube = $this->youtube;

        parse_str(parse_url($homeRowItem->getPlaylistId(), PHP_URL_QUERY), $parameters);
        $playlistId = $parameters['list'];

        try {
            $info = $youtube->getPlaylistInfo($playlistId)->getItems();
        } catch (\Exception $e) {
            $this->logger->error("Call to YouTube failed with the message \"".$e->getErrors()[0]['message']."\"");
            return Array();
        }

        $info = $info[0];
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

            $title = $info->getSnippet()->getTitle();
            $link = 'https://www.youtube.com/playlist?list='.$info->getId();

            if ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_GAMERSX) {
                $link = '/channel/'.$info->getId();
            } elseif ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_CUSTOM) {
                $link = $homeRowItem->getCustomLink();
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
                    'elementId' => uniqid('embed-'),
                ];
            } else {
                $embedData = NULL;
            }

            $channels = [
                [
                    'info' => $info,
                    'broadcast' => $broadcast,
                    'liveViewerCount' => $broadcast ? $broadcast['viewer_count'] : 0,
                    'viewedCount' => isset($info['statistics_view_count']) ? (int) $info['statistics_view_count'] : 0,
                    'showOnline' => $broadcast !== NULL,
                    'onlineDisplay' => [
                        'title' => $title,
                        'showArt' => $homeRowItem->getShowArt(),
                        'showEmbed' => TRUE,
                        'showOverlay' => TRUE,
                    ],
                    'offlineDisplay' => [
                        'title' => $info->getSnippet()->getTitle(),
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
                    'embedName' => 'YouTubeEmbed',
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
