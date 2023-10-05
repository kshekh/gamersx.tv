<?php

namespace App\Containerizer;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Entity\HomeRowItemOperation;
use App\Service\HomeRowInfo;

class TwitchGameContainerizer extends LiveContainerizer implements ContainerizerInterface
{
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
        $gameIds = $homeRowItem->getTopic()['topicId'];

        $infos = $twitch->getGameInfo($gameIds);
        if (200 !== $infos->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$infos->getStatusCode());
            unset($infos);
            return Array();
        }


        // Get the info for the game, use that for every game
        $info = $infos->toArray()['data'];

        if (!isset($info) || empty($info)) {
            return Array();
        }

        $info = $info[0];
        $options = $homeRowItem->getSortAndTrimOptions();
        $maxContainers = (isset($options['maxContainers']))?$options['maxContainers']:0;
        // Get every broadcast
        $broadcasts = [];
        // getting selected game streamers
        $getSelectedRowItemOperation =  $homeRowItem->getHomeRowItemOperations();
        $selectedStreamerArr = [];
        $blacklistedUserIds = [];
        if(!empty($getSelectedRowItemOperation)) {
            foreach ($getSelectedRowItemOperation as $getSelectedOprData) {
                if($getSelectedOprData->getIsBlacklisted() == 0 && $getSelectedOprData->getIsFullSiteBlacklisted() == 0) {
                    $user_broadcasts = $twitch->getStreamForStreamer($getSelectedOprData->getUserId());
                    if ($user_broadcasts->getStatusCode() == 200) {
                        $user_broadcasts = $user_broadcasts->toArray()['data'];
                        foreach ($user_broadcasts as $user_broadcast_data) {
                            $broadcasts[] = $user_broadcast_data;
                        }

                        $selectedStreamerArr[$getSelectedOprData->getUserId()] = [
                            'priority' => $getSelectedOprData->getPriority()
                        ];
                    }
                } else {
                    $blacklistedUserIds[$getSelectedOprData->getUserId()] = [
                        'priority' => $getSelectedOprData->getPriority()
                    ];
                }
            }

            if(count($broadcasts) < $maxContainers) {
                $remaining_broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 20);
                if ($remaining_broadcasts->getStatusCode() == 200) {
                    $remaining_broadcasts_Arr = $remaining_broadcasts->toArray()['data'];
                    foreach ($remaining_broadcasts_Arr as $rem_broadcast_data) {
                        if(!isset($selectedStreamerArr[$rem_broadcast_data['user_id']]) && !isset($blacklistedUserIds[$rem_broadcast_data['user_id']])) {
                            $broadcasts[] = $rem_broadcast_data;
                        }

                        if(count($broadcasts) == $maxContainers) {
                            break;
                        }
                    }
                }
            }

        } else {

            $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 20);
            if (200 !== $broadcasts->getStatusCode()) {
                $this->logger->error("Call to Twitch failed with ".$broadcasts->getStatusCode());
                unset($broadcasts);
                return Array();
            }
            $broadcasts = $broadcasts->toArray()['data'];
        }


        $rowName = $homeRowItem->getHomeRow()->getTitle();
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
            switch ($homeRowItem->getLinkType()) {
            case HomeRowItem::LINK_TYPE_GAMERSX:
                $link = '/game/'.$info['id'];
                break;
            case HomeRowItem::LINK_TYPE_EXTERNAL:
                $link = 'https://www.twitch.tv/directory/game/'.$info['name'];
                break;
            case HomeRowItem::LINK_TYPE_CUSTOM:
                $link = $homeRowItem->getCustomLink();
                break;
            default:
                $link = '#';
            }

            $channels = Array();
            foreach ($broadcasts as $i => $broadcast) {
                $title = sprintf("%s playing %s for %d viewers",
                    $broadcast['user_name'], $broadcast['game_name'], $broadcast['viewer_count']);

                $streamerInfo = $twitch->getStreamerInfoByChannel($broadcast['user_login']);
                $streamerInfo = $streamerInfo->toArray();
                $channels[] = [
                    'info' => $info,
                    'profileImageUrl' => $streamerInfo['data'][0]['profile_image_url'],
                    'broadcast' => $broadcast,
                    'liveViewerCount' => $broadcast ? $broadcast['viewer_count'] : 0,
                    'viewedCount' => isset($info['view_count']) ? $info['view_count'] : 0,
                    'showOnline' => TRUE,
                    'onlineDisplay' => [
                        'title' => $title,
                        'showArt' => FALSE,
                        'showEmbed' => TRUE,
                        'showOverlay' => TRUE,
                    ],
                    'offlineDisplay' => [
                        'title' => $info['name'],
                        'showArt' => FALSE,
                        'showEmbed' => FALSE,
                        'showOverlay' => FALSE,
                    ],
                    'itemType' => $homeRowItem->getItemType(),
                    'rowName' => $rowName,
                    'sortIndex' => $i,
                    'image' => NULL,
                    'overlay' => $this->uploader->asset($this->homeRowItem, 'overlayArtFile'),
                    'customArt' => $this->uploader->asset($this->homeRowItem, 'customArtFile'),
                    'link' => $link,
                    'componentName' => 'EmbedContainer',
                    'embedName' => 'TwitchEmbed',
                    'embedData' => [
                        'channel' => $broadcast['user_login'],
                        'elementId' => uniqid('embed-'),
                    ],
                    'description' => $description
                ];
            }

            $this->items = $channels;
            $this->options = $homeRowItem->getSortAndTrimOptions();

            // If showArt is checked, add the art to the very first item
            if ($homeRowItem->getShowArt() === TRUE) {
                // Get the sized art link
                $imageWidth = 225;
                $imageHeight = 300;
                $imageUrl = $info['box_art_url'];
                $imageUrl = str_replace('{height}', $imageHeight, $imageUrl);
                $imageUrl = str_replace('{width}', $imageWidth, $imageUrl);

                $image = [
                    'url' => $imageUrl,
                    'class' => 'box-art',
                    'width' => $imageWidth,
                    'height' => $imageHeight,
                ];

                $this->items[0]['image'] = $image;
                $this->items[0]['onlineDisplay']['showArt'] = TRUE;
                $this->items[0]['offlineDisplay']['showArt'] = TRUE;
            }

            $this->sort();
            $this->trim();

            return $this->items;
        }

        return Array();
    }
}
