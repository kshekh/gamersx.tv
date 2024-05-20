<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;
use App\Traits\ErrorLogTrait;
use Symfony\Component\HttpClient\Exception\ClientException;

class TwitchVideoContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    use ErrorLogTrait;

    private $homeRowItem;
    private $twitch;
    private $entityManager;

    public function __construct(HomeRowItem $homeRowItem, $twitch,$entityManager)
    {
        $this->homeRowItem = $homeRowItem;
        $this->twitch = $twitch;
        $this->entityManager = $entityManager;
    }

    public function getContainers(): Array
    {
        try {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb->select('hri')
            ->from('App:HomeRowItem', 'hri')
            ->where('hri.id != :id')
            ->setParameter('id', $this->homeRowItem->getId())
            ->andWhere('hri.is_unique_container = 0')
            ->andWhere('hri.isPublished = 1')
            ->andWhere('hri.videoId = :videoId')
            ->setParameter('videoId', $this->homeRowItem->getVideoId());

        $check_unique_item = $query->getQuery()->getResult();

        $is_unique_container =  $this->homeRowItem->getIsUniqueContainer();
        if($is_unique_container == 0 && (isset($check_unique_item) && !empty($check_unique_item) && count($check_unique_item) > 1 && $check_unique_item[0]['id'] != $this->homeRowItem->getId())) {
            return Array();
        }


        $homeRowInfo = new HomeRowInfo();
        $homeRowItem = $this->homeRowItem;
        $twitch = $this->twitch;

        $separatedUrl = explode('/', parse_url($homeRowItem->getVideoId(), PHP_URL_PATH));

        $parent = 'gamersx.tv';
        if ($separatedUrl[1] == 'videos') {
            $videoId = $separatedUrl[2];
            $embedUrl = 'https://player.twitch.tv/?parent='.$parent.'&video='.$videoId;
        } else {
            $videoId = $separatedUrl[3];
            $embedUrl = 'https://clips.twitch.tv/embed?clip='.$videoId.'&parent='.$parent;
        }

        try {
            if ($separatedUrl[1] == 'videos') {
                $info = $twitch->getVideoInfo($videoId)->toArray();
            } else {
                $info = $twitch->getClipInfo($videoId)->toArray();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage()." ".$e->getFile() . " " .$e->getLine();
            $this->logger->error($msg);
            $this->log_error($msg, 500, "twitch_video_containerizer", $this->homeRowItem->getId());
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

            $title = $info['title'];
            $link = $homeRowItem->getVideoId();

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
                'type' => $separatedUrl[1] != 'videos' ? 'twitch_clip' : 'twitch_video'
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
        } catch (ClientException $th) {
            $msg = $th->getMessage()." ".$th->getFile() . " " .$th->getLine();
            $this->logger->error($msg);
            $this->log_error($msg, $th->getCode(), "twitch_video_containerizer", $this->homeRowItem ? $this->homeRowItem->getId() : null);
        } catch (\Exception $ex) {
            $msg = $ex->getMessage()." ".$ex->getFile() . " " .$ex->getLine();
            $this->logger->error($msg);
            $this->log_error($msg, $ex->getCode(), "twitch_video_containerizer", $this->homeRowItem ? $this->homeRowItem->getId() : null);
        }
        return Array();
    }
}
