<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;
use App\Traits\ErrorLogTrait;

class TwitchStreamerContainerizer extends LiveContainerizer implements ContainerizerInterface
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
        $topic_id = $this->homeRowItem->getTopic()['topicId'];
        $check_unique_item =  $this->entityManager->getRepository(HomeRowItem::class)->findUniqueItem('topicId',$topic_id);

        $is_unique_container =  $this->homeRowItem->getIsUniqueContainer();
        if($is_unique_container == 0 && (isset($check_unique_item) && !empty($check_unique_item) && count($check_unique_item) > 1 && $check_unique_item[0]['id'] != $this->homeRowItem->getId())) {
            return Array();
        }

        $homeRowInfo = new HomeRowInfo();
        $homeRowItem = $this->homeRowItem;
        $twitch = $this->twitch;

        $streamerId = $homeRowItem->getTopic()['topicId'];

        $infos = $twitch->getStreamerInfo($streamerId);
        if (200 !== $infos->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$infos->getStatusCode());
            $this->log_error($infos->getContent(false), $infos->getStatusCode(), "twitch_streaming_container_info");
            unset($infos);
            return Array();
        }

        $broadcast = $twitch->getStreamForStreamer($streamerId);
        if (200 !== $broadcast->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$broadcast->getStatusCode());
            $this->log_error($broadcast->getContent(false), $broadcast->getStatusCode(), "twitch_streaming_container_broadcast");
            unset($broadcast);
            return Array();
        }

        $info = $infos->toArray()['data'];

        if (!isset($info) || empty($info)) {
            return Array();
        }

        $info = $info[0];
        $broadcast = $broadcast->toArray()['data'];
        $broadcast = !empty($broadcast) ? $broadcast[0] : NULL;

        $title = $broadcast === NULL ? $info['display_name'] : sprintf("%s playing %s for %d viewers",
            $broadcast['user_name'], $broadcast['game_name'], $broadcast['viewer_count']);
        $description = $homeRowItem->getDescription();
        $currentTime = $homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));

        $isPublished = $homeRowItem->getIsPublished();

        if (!$isPublished) {
            return Array();
        }
        // getting selected games
        $getSelectedRowItemOperation =  $homeRowItem->getHomeRowItemOperations();
        $selectedGamesArr = [];
        if(!empty($getSelectedRowItemOperation)) {
            foreach ($getSelectedRowItemOperation as $getSelectedOprData) {
                $selectedGamesArr[$getSelectedOprData->getGameId()] = [
                    'is_whitelisted' => $getSelectedOprData->getIsWhitelisted(),
                    'is_blacklisted' => $getSelectedOprData->getIsBlacklisted(),
                ];
            }
        }

        if(!empty($broadcast)) {
            // sorting streamers based on priority
            $selected_streamer_index = [];
            if(isset($selectedGamesArr[$broadcast['game_id']])) {
                $selected_streamer_index = $selectedGamesArr[$broadcast['game_id']];
            }
            if(!empty($selected_streamer_index)) {
                $is_blacklisted =  $selected_streamer_index['is_blacklisted'];
                $is_whitelisted =  $selected_streamer_index['is_whitelisted'];
                if($is_blacklisted == 1 || $is_whitelisted == 0) {
                    return Array();
                }
            }
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
                    'profileImageUrl' => $info['profile_image_url'],
                    'liveViewerCount' => $broadcast ? $broadcast['viewer_count'] : 0,
                    'viewedCount' => isset($info['view_count']) ? $info['view_count'] : 0,
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
