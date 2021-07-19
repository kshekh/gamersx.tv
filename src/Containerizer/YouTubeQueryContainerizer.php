<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class YouTubeQueryContainerizer extends LiveContainerizer implements ContainerizerInterface
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

        try {
            $broadcasts = $youtube->searchLiveChannels($query, $max, null, null);
        } catch (\Exception $e) {
            $this->logger->error("Call to YouTube failed with the message \"".$e->getErrors()[0]['message']."\"");
            return Array();
        }


        foreach ($broadcasts->getItems() as $i => $broadcast) {
            $snippet = $broadcast->getSnippet();

            $title = sprintf("%s - \"%s\"",
                $snippet->getChannelTitle(), $snippet->getTitle());

            if ($homeRowItem->getLinkType() === HomeRowItem::LINK_TYPE_GAMERSX) {
                $link = '/query/'.$query;
            } else {
                $link = 'https://www.youtube.com/v/'.$broadcast->getId()->getVideoId();
            }

            $channel = [
                'info' => $snippet,
                'broadcast' =>  $broadcast,
                'liveViewerCount' => 0,
                'viewedCount' => 0,
                'showOnline' => TRUE,
                'onlineDisplay' => [
                    'title' => $title,
                    'showArt' => FALSE,
                    'showEmbed' => TRUE,
                ],
                'offlineDisplay' => [
                    'title' => $title,
                    'showArt' => FALSE,
                    'showEmbed' => FALSE,
                ],
                'itemType' => $homeRowItem->getItemType(),
                'rowName' => $homeRowItem->getHomeRow()->getTitle(),
                'sortIndex' => $homeRowItem->getSortIndex(),
                'image' => NULL,
                'customArt' => $this->uploader->asset($this->homeRowItem, 'customArtFile'),
                'link' => $link,
                'componentName' => 'EmbedContainer',
                'embedName' => 'YouTubeEmbed',
                'embedData' => [
                    'overlay' => $this->uploader->asset($this->homeRowItem, 'overlayArtFile'),
                    'video' => $broadcast->getId()->getVideoId(),
                    'elementId' => 'embed-'.sha1($snippet->getTitle())
                ]
            ];

            $channels[] = $channel;
        }

        $this->items = $channels;

        $this->sort();
        $this->trim();

        return $this->items;
    }

}
