<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;
use App\Service\TwitchApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TwitchStreamerContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    private HomeRowItem $homeRowItem;
    private TwitchApi $twitch;
    private EntityManagerInterface $entityManager;

    public function __construct(HomeRowItem $homeRowItem, TwitchApi $twitch, EntityManagerInterface $entityManager)
    {
        $this->homeRowItem = $homeRowItem;
        $this->twitch = $twitch;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \Exception
     */
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
            $twitch = $this->twitch;

            $streamerId = $homeRowItem->getTopic()['topicId'];

            $infos = $twitch->getStreamerInfo($streamerId);
            if (200 !== $infos->getStatusCode()) {
                $this->logger->error("Call to Twitch failed with ".$infos->getStatusCode());
                unset($infos);
                return Array();
            }

            $broadcast = $twitch->getStreamForStreamer($streamerId);
            if (200 !== $broadcast->getStatusCode()) {
                $this->logger->error("Call to Twitch failed with ".$broadcast->getStatusCode());
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

                $link = match ($homeRowItem->getLinkType()) {
                    HomeRowItem::LINK_TYPE_GAMERSX => '/streamer/' . $info['id'],
                    HomeRowItem::LINK_TYPE_EXTERNAL => 'https://www.twitch.tv/' . $info['login'],
                    HomeRowItem::LINK_TYPE_CUSTOM => $homeRowItem->getCustomLink(),
                    default => '#',
                };

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
        } catch (\Exception $e) {
            dd($e->getMessage() . "\n" . $e->getLine());
        }
    }
}
