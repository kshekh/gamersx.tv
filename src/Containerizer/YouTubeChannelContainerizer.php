<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;
use App\Service\YouTubeApi;
use Doctrine\ORM\EntityManagerInterface;

class YouTubeChannelContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    private HomeRowItem $homeRowItem;
    private YouTubeApi $youtube;
    private EntityManagerInterface $entityManager;

    public function __construct(HomeRowItem $homeRowItem, YouTubeApi $youtube, EntityManagerInterface $entityManager)
    {
        $this->homeRowItem = $homeRowItem;
        $this->youtube = $youtube;
        $this->entityManager = $entityManager;
    }

    public function getContainers(): array
    {
        try {
            $topic_id = $this->homeRowItem->getTopic()['topicId'];
            $check_unique_item =  $this->entityManager->getRepository(HomeRowItem::class)->findUniqueItem('topicId',$topic_id);
            $uniqueIds = $check_unique_item->fetchFirstColumn();
            $is_unique_container =  $this->homeRowItem->getIsUniqueContainer();

            if($is_unique_container == 0 && count($uniqueIds) && $uniqueIds[0] != $this->homeRowItem->getId()) {
                return Array();
            }

            $homeRowInfo = new HomeRowInfo();
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
            $description = $homeRowItem->getDescription();
            $currentTime = $homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));
            $liveViewers = 0;
            if ($broadcast) {
                $videoDetails = $youtube->getVideoInfo($broadcast->getId()->getVideoId())->getItems();
                $liveViewers = $videoDetails[0]->liveStreamingDetails->concurrentViewers;
            }
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

                if ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_GAMERSX) {
                    $link = '/channel/'.$info->getId();
                } elseif ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_CUSTOM) {
                    $link = $homeRowItem->getCustomLink();
                }

                if (($broadcast === NULL && $homeRowItem->getOfflineDisplayType() === HomeRowItem::OFFLINE_DISPLAY_ART) ||
                    $homeRowItem->getShowArt() === TRUE) {
                    // Get the sized art link
                    $imageInfo = $info->getSnippet()->getThumbnails();
                    $imageInfo = $imageInfo->getMedium() ?: $imageInfo->getStandard();

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

                if ($info !== NULL) {
                    if ($info->getStatistics() !== NULL) {
                        $info['statistics_view_count'] = $info->getStatistics()->getViewCount();
                    }
                }

                $channels = [
                    [
                        'info' => $info,
                        'broadcast' => $broadcast,
                        'liveViewerCount' => $liveViewers,
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
        } catch (\Exception $e) {
            dd($e->getMessage() . "\n" . $e->getLine());
        }
    }
}
