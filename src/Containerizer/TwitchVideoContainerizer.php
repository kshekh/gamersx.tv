<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;
use App\Service\TwitchApi;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use App\Traits\ErrorLogTrait;
use Symfony\Component\HttpClient\Exception\ClientException;

class TwitchVideoContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    use ErrorLogTrait;

    private HomeRowItem $homeRowItem;
    private TwitchApi $twitch;
    private $entityManager;

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
     * @throws Exception
     */
    public function getContainers(): array
    {
        /*
         * The following query looks for an item or items from home_row_items that match the following conditions:
         * - The ID does not match the ID of the current homeRowItem instance
         * - The field is_unique_container is set to false
         * - The field isPublished is set to true
         * - The videoId matches the videoId of the current HomeRowItem instance
         */
        try {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb->select('hri')
            ->from(HomeRowItem::class, 'hri')
            ->where('hri.id != :id')
            ->setParameter('id', $this->homeRowItem->getId())
            ->andWhere('hri.is_unique_container = 0')
            ->andWhere('hri.isPublished = 1')
            ->andWhere('hri.videoId = :videoId')
            ->setParameter('videoId', $this->homeRowItem->getVideoId());

//        if ($this->homeRowItem->getVideoId() !== 'https://www.twitch.tv/videos/1886732468') {
//            dd($query->getQuery()->execute(), $this->homeRowItem->getId(), $this->homeRowItem->getVideoId());
//        }
//        dd($qb->select('hri')->from(HomeRowItem::class, 'hri')->where('hri.itemType = :itemType')->setParameter('itemType', 'twitch_video')->getQuery()->execute());
        $is_unique_container =  $this->homeRowItem->getIsUniqueContainer();
        if($is_unique_container == 0 && (!empty($check_unique_item) && count($check_unique_item) > 1 && array_key_exists(0, $check_unique_item) && $check_unique_item[0]['id'] != $this->homeRowItem->getId())) {
            return Array();
        }

        $homeRowInfo = new HomeRowInfo();
        $homeRowItem = $this->homeRowItem;
        $twitch = $this->twitch;

        /*
         * Contrary to its name videoId contains a full URL instead of just an ID
         * PHP_URL_PATH: Outputs the path of the URL parsed. https://www.php.net/manual/en/url.constants.php#constant.php-url-path
         */
        $separatedUrl = explode('/', parse_url($homeRowItem->getVideoId(), PHP_URL_PATH));

        if (! array_key_exists(0, $separatedUrl)) {
            return Array();
        }

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

        $info = $info['data'] ? $info['data'][0] : null;
        if (is_null($info)) {
            return Array();
        }
//        dd($this->homeRowItem->getTimezone());
        $broadcast = null;
        $description = $homeRowItem->getDescription();
//        $currentTime = $homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));
        $currentTime = new DateTime('now', new DateTimeZone($this->homeRowItem->getTimezone()));

        $isPublished = $homeRowItem->getIsPublished();

        if (!$isPublished) {
            return Array();
        }

        $isPublishedStartTime = new DateTime($this->homeRowItem->getIsPublishedStart(), new DateTimeZone($this->homeRowItem->getTimezone()));
        $isPublishedEndTime = new DateTime($this->homeRowItem->getIsPublishedEnd(), new DateTimeZone($this->homeRowItem->getTimezone()));
//        dd($currentTime, $isPublishedStartTime, $isPublishedEndTime);
//        $isPublishedStartTime = $homeRowInfo->convertHoursMinutesToSeconds(dateTime: $homeRowItem->getIsPublishedStart());
//        $isPublishedEndTime = $homeRowInfo->convertHoursMinutesToSeconds(dateTime: $homeRowItem->getIsPublishedEnd());
//        dd($currentTime, $isPublishedStartTime, $isPublishedEndTime);
        /*
         * The following condition checks if:
         * - isPublishedStartTime is not null
         * - isPublishedEndTime is not null
         * - currentTime is greater than or equal to isPublishedStartTime
         * - currentTime is less than or equal to isPublishedEndTime
         */
        if ($currentTime >= $isPublishedStartTime && $currentTime <= $isPublishedEndTime) {
//        if (
//            !is_null($isPublishedStartTime) && !is_null($isPublishedEndTime) &&
//            (($currentTime >= $isPublishedStartTime) && ($currentTime <= $isPublishedEndTime))
//        ) {
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

            if ($info['view_count'] !== NULL) {
                $info['statistics_view_count'] = $info['view_count'];
            }
//            dd('This far');
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
