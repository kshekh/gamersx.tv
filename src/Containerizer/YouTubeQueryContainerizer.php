<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;
use App\Traits\ErrorLogTrait;
use Symfony\Component\HttpClient\Exception\ClientException;

class YouTubeQueryContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    use ErrorLogTrait;

    private $homeRowItem;
    private $youtube;

    public function __construct(HomeRowItem $homeRowItem, $youtube)
    {
        $this->homeRowItem = $homeRowItem;
        $this->youtube = $youtube;
    }

    public function getContainers(): Array
    {
        try {
        $homeRowInfo = new HomeRowInfo();
        $homeRowItem = $this->homeRowItem;
        $youtube = $this->youtube;

        $this->options = $homeRowItem->getSortAndTrimOptions();
        if (array_key_exists('maxContainers', $this->options)) {
            $max = $this->options['maxContainers'];
        }
        if (array_key_exists('maxLive', $this->options)) {
            $max = $this->options['maxLive'];
        }

        $query = $homeRowItem->getTopic()['topicId'];
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
            // try {
                $broadcasts = $youtube->searchLiveChannels($query, $max, null, null)->getItems();
            // } catch (\Exception $e) {
            //     $this->logger->error("Call to YouTube failed with the message \"".$e->getErrors()[0]['message']."\"");
            //     return Array();
            // }

            $videoIds = [];
            foreach ($broadcasts as $broadcast) {
                $videoIds[] = $broadcast->getId()->getVideoId();
            }

            $channels = [];
            $videoStats = $youtube->getChannelVideosStats($videoIds)->getItems();

            foreach ($broadcasts as $i => $broadcast) {
                $snippet = $broadcast->getSnippet();

                $title = sprintf("%s - \"%s\"",
                    $snippet->getChannelTitle(), $snippet->getTitle());

                if ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_GAMERSX) {
                    $link = '/query/'.$query;
                } elseif ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_CUSTOM) {
                    $link = $homeRowItem->getCustomLink();
                } else {
                    $link = 'https://www.youtube.com/v/'.$broadcast->getId()->getVideoId();
                }

                $broadcast['view_count'] = $videoStats[$i]->getStatistics()->getViewCount();
                $broadcast['viewer_count'] = $videoStats[$i]->getLiveStreamingDetails()->getConcurrentViewers();

                $channel = [
                    'info' => $snippet,
                    'broadcast' =>  $broadcast,
                    'liveViewerCount' => $broadcast ? $broadcast['viewer_count'] : 0,
                    'viewedCount' => isset($broadcast['view_count']) ? $broadcast['view_count'] : 0,
                    'showOnline' => TRUE,
                    'onlineDisplay' => [
                        'title' => $title,
                        'showArt' => FALSE,
                        'showEmbed' => TRUE,
                        'showOverlay' => TRUE,
                    ],
                    'offlineDisplay' => [
                        'title' => $title,
                        'showArt' => FALSE,
                        'showEmbed' => FALSE,
                        'showOverlay' => FALSE,
                    ],
                    'itemType' => $homeRowItem->getItemType(),
                    'rowName' => $homeRowItem->getHomeRow()->getTitle(),
                    'sortIndex' => $homeRowItem->getSortIndex(),
                    'image' => NULL,
                    'overlay' => $this->uploader->asset($this->homeRowItem, 'overlayArtFile'),
                    'customArt' => $this->uploader->asset($this->homeRowItem, 'customArtFile'),
                    'link' => $link,
                    'componentName' => 'EmbedContainer',
                    'embedName' => 'YouTubeEmbed',
                    'embedData' => [
                        'video' => $broadcast->getId()->getVideoId(),
                        'elementId' => uniqid('embed-'),
                    ],
                    'description' => $description
                ];

                $channels[] = $channel;
            }

            $this->items = $channels;

            $this->sort();
            $this->trim();

            return $this->items;
        }
        } catch (ClientException $th) {
            $msg = $th->getMessage(). " " . $th->getFile() . " " . $th->getLine();
            $this->log_error($msg, $th->getCode(), "youtube_query_containerizer",  $this->homeRowItem ? $this->homeRowItem->getId() : null);
            $this->logger->error($msg);
        } catch (\Exception $ex) {
            $msg = $ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine();
            $this->log_error($msg, $ex->getCode(), "youtube_query_containerizer",  $this->homeRowItem ? $this->homeRowItem->getId() : null);
            $this->logger->error($msg);
        }

        return Array();
    }
}
