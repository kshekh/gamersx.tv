<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;

class YouTubeVideoContainerizer extends LiveContainerizer implements ContainerizerInterface
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
        $homeRowInfo = new HomeRowInfo();
        $homeRowItem = $this->homeRowItem;
        $youtube = $this->youtube;

        parse_str(parse_url($homeRowItem->getVideoId(), PHP_URL_QUERY), $parameters);
        $videoId = $parameters['v'];

        try {
            $info = $youtube->getVideoInfo($videoId)->getItems();
        } catch (\Exception $e) {
            $this->logger->error("Call to YouTube failed with the message \"".$e->getMessage()."\"");
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

        $isPublishedStartTime = $homeRowInfo->convertHoursMinutesToSeconds($homeRowItem->getIsPublishedStart());
        $isPublishedEndTime = $homeRowInfo->convertHoursMinutesToSeconds($homeRowItem->getIsPublishedEnd());

        if (
            !is_null($isPublishedStartTime) && !is_null($isPublishedEndTime) &&
            (($currentTime >= $isPublishedStartTime) && ($currentTime <= $isPublishedEndTime))
        ) {
            // No need for a container if we're not displaying and not online
            if (($homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_NONE) &&
                $broadcast === NULL) {
                return Array();
            }
            /*Get channel's profile picture*/
            $channel_id = $info->getSnippet()->getChannelId();
            try {
                $channel_info = $youtube->getChannelInfo($channel_id)->getItems();
            } catch (\Exception $e) {
            }

            $channelThumbnails = $channel_info[0]->getSnippet()->getThumbnails();
            $channelThumbnailInfo = $channelThumbnails->getMedium() ? $channelThumbnails->getMedium() : $channelThumbnails->getDefault();

            $title = $info->getSnippet()->getTitle();
            $link = 'https://www.youtube.com/watch?v='.$info->getId();


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

            $embedData = [
                'video' => $info->getId(),
                'elementId' => uniqid('embed-'),
                'url' => 'https://www.youtube.com/embed/'.$info->getId(),
            ];

            if ($info !== NULL) {
                if ($info->getStatistics() !== NULL) {
                    $info['statistics_view_count'] = $info->getStatistics()->getViewCount();
                }
            }
            /*Fetch viewer count and display in container*/
            $viewer_count = $broadcast ? $broadcast['viewer_count'] : 0;
            $statistics_view_count = isset($info['statistics_view_count']) ? (int) $info['statistics_view_count'] : 0;
            if($viewer_count == 0) {
                $viewer_count = $statistics_view_count;
            }
            $channels = [
                [
                    'info' => $info,
                    'broadcast' => $broadcast,
                    'profileImageUrl' => $channelThumbnailInfo->getUrl(),
                    'liveViewerCount' => $viewer_count,
                    'viewedCount' => $statistics_view_count,
                    'showOnline' => TRUE,
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
