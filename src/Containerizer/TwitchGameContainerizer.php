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
        $gameIds = $homeRowItem->getTopic()['topicId'];

        $infos = $twitch->getGameInfo($gameIds);
        if (200 !== $infos->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$infos->getStatusCode());
            unset($infos);
            return Array();
        }

        $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 20);
        if (200 !== $broadcasts->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$broadcasts->getStatusCode());
            unset($broadcasts);
            return Array();
        }

        // Get the info for the game, use that for every game
        $info = $infos->toArray()['data'];

        if (!isset($info) || empty($info)) {
            return Array();
        }

        $info = $info[0];

        // Get every broadcast
        $broadcasts = $broadcasts->toArray()['data'];

        // getting selected game streamers
        $getSelectedRowItemOperation =  $homeRowItem->getHomeRowItemOperations();
        $selectedStreamerArr = [];
        if(!empty($getSelectedRowItemOperation)) {
            foreach ($getSelectedRowItemOperation as $getSelectedOprData) {
                $selectedStreamerArr[$getSelectedOprData->getStreamerId()] = [
                    'is_blacklisted' => $getSelectedOprData->getIsBlacklisted(),
                    'is_full_site_blacklisted' => $getSelectedOprData->getIsFullSiteBlacklisted(),
                    'priority' => $getSelectedOprData->getPriority()
                ];
            }
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

            // sorting streamers based on priority
            if(isset($this->items)) {
                foreach ($this->items as $res_key => $res_data) {
                    $broadcast_data = $res_data['broadcast'];
                    $selected_streamer_index = [];
                    if(isset($selectedStreamerArr[$broadcast_data['id']])) {
                        $selected_streamer_index = $selectedStreamerArr[$broadcast_data['id']];
                    } else if(isset($selectedStreamerArr[$broadcast_data['user_id']])) {
                        $selected_streamer_index = $selectedStreamerArr[$broadcast_data['user_id']];
                    }
                    if(!empty($selected_streamer_index)) {
                        $is_blacklisted =  $selected_streamer_index['is_blacklisted'];
                        $is_full_site_blacklisted =  $selected_streamer_index['is_full_site_blacklisted'];
                        $priority = $selected_streamer_index['priority'];
                        if($is_blacklisted == 1 || $is_full_site_blacklisted == 1) {
                            unset($this->items[$res_key]);
                        } else {
                            $this->items[$res_key]['priority'] = $priority;
                        }
                    } else {
                        $this->items[$res_key]['priority'] = (count($this->items)+1);
                    }
                }

                if (array_key_exists('itemSortType', $this->options)) {
                    $sort = $this->options['itemSortType'];
                    if ($sort === HomeRow::SORT_ASC) {
                        $priority = array_column($this->items, 'priority');
                        array_multisort($priority, SORT_ASC, $this->items);
                    } elseif ($sort === HomeRow::SORT_DESC) {
                        $priority = array_column($this->items, 'priority');
                        array_multisort($priority, SORT_DESC, $this->items);
                    } elseif ($sort === HomeRow::SORT_FIXED) {
                        $priority = array_column($this->items, 'sortIndex');
                        array_multisort($priority, SORT_ASC, $this->items);
                    }
                }

            }

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

            $this->trim();

            return $this->items;
        }

        return Array();
    }
}
