<?php

namespace App\Containerizer;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;
use App\Service\TwitchApi;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use App\Traits\ErrorLogTrait;
use Symfony\Component\HttpClient\Exception\ClientException;

class TwitchGameContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    use ErrorLogTrait;
    public function __construct(
        private readonly HomeRowItem            $homeRowItem,
        private readonly TwitchApi              $twitch,
        private $entityManager
    ){}

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getContainers(): array
    {
        try {
            $topic_id = $this->homeRowItem->getTopic()['topicId'];
            $repository = $this->entityManager->getRepository(HomeRowItem::class);
            $uniqueIds = $repository->findUniqueItem(
                key: 'topicId',
                value: $topic_id
            );

            $is_unique_container =  $this->homeRowItem->getIsUniqueContainer();

            if($is_unique_container == 0 && count($uniqueIds) && $uniqueIds[0] != $this->homeRowItem->getId()) {
                return Array();
            }

            $homeRowInfo = new HomeRowInfo();
            $homeRowItem = $this->homeRowItem;
            $twitch = $this->twitch;
            $gameIds = $homeRowItem->getTopic()['topicId'];

            $infos = $twitch->getGameInfo($gameIds);
            if ($infos->getStatusCode() !== 200) {
                $this->logger->error("Call to Twitch failed with " . $infos->getStatusCode());
                $this->log_error($infos->getContent(false), $infos->getStatusCode(), "twitch_game_container_info", $this->homeRowItem ? $this->homeRowItem->getId() : null);
            unset($infos);
                return Array();
            }


            // Get the info for the game, use that for every game
            $info = $infos->toArray()['data'];

            if (empty($info)) {
                return Array();
            }

            $info = $info[0];
            $options = $homeRowItem->getSortAndTrimOptions();
            $maxContainers = $options['maxContainers'] ?? 0;
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
                    $this->log_error($broadcasts->getContent(false), $broadcasts->getStatusCode(), "twitch_game_container_broadcast");
                unset($broadcasts);
                    return Array();
                }
                $broadcasts = $broadcasts->toArray()['data'];
            }


            $rowName = $homeRowItem->getHomeRow()->getTitle();
            $description = $homeRowItem->getDescription();
            $timezone = $this->homeRowItem->getTimezone() ?? 'America/Los_Angeles';
            $currentTime = new DateTime('now', new DateTimeZone($timezone));
//             $currentTime = $homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));

            $isPublished = $homeRowItem->getIsPublished();

            if (!$isPublished) {
                return Array();
            }
//            if($homeRowItem->getId() === 280) dd('I am 280');
//            if($homeRowItem->getId() === 333) dd('I am 333');
//             $isPublishedStartTime = $homeRowInfo->convertHoursMinutesToSeconds($homeRowItem->getIsPublishedStart());
//             $isPublishedEndTime = $homeRowInfo->convertHoursMinutesToSeconds($homeRowItem->getIsPublishedEnd());
            $isPublishedStartTime = new DateTime($this->homeRowItem->getIsPublishedStart(), new DateTimeZone($timezone));
            $isPublishedEndTime = new DateTime($this->homeRowItem->getIsPublishedEnd(), new DateTimeZone($timezone));

            if ($currentTime >= $isPublishedStartTime && $currentTime <= $isPublishedEndTime) {
                $link = match ($homeRowItem->getLinkType()) {
                    HomeRowItem::LINK_TYPE_GAMERSX => '/game/' . $info['id'],
                    HomeRowItem::LINK_TYPE_EXTERNAL => 'https://www.twitch.tv/directory/game/' . $info['name'],
                    HomeRowItem::LINK_TYPE_CUSTOM => $homeRowItem->getCustomLink(),
                    default => '#',
                };

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
                    $imageUrl = str_replace('{height}', (string)$imageHeight, $imageUrl);
                    $imageUrl = str_replace('{width}', (string)$imageWidth, $imageUrl);

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
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "twitch_game_containerizer", $this->homeRowItem ? $this->homeRowItem->getId() : null);
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "twitch_game_containerizer", $this->homeRowItem ? $this->homeRowItem->getId() : null);
        }

            return Array();

    }
}
