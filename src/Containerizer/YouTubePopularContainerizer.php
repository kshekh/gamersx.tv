<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class YouTubePopularContainerizer extends LiveContainerizer implements ContainerizerInterface
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

        $broadcasts = $youtube->getPopularStreams();

        $channels = Array();

        foreach ($broadcasts->getItems() as $i => $broadcast) {
            $snippet = $broadcast->getSnippet();

            $title = sprintf("%s - \"%s\"",
                $snippet->getChannelTitle(), $snippet->getTitle());

            $channel = [
                'info' => $snippet,
                'broadcast' =>  $broadcast,
                'title' => $title,
                'itemType' => $homeRowItem->getItemType(),
                'rowName' => $homeRowItem->getHomeRow()->getTitle(),
                'sortIndex' => $homeRowItem->getSortIndex(),
                'image' => NULL,
                'link' => 'https://www.youtube.com/v/'.$broadcast->getId(),
                'componentName' => 'EmbedContainer',
                'embedName' => 'YouTubeEmbed',
                'embedData' => [
                    'video' => $broadcast->getId(),
                    'elementId' => 'embed-'.sha1($snippet->getTitle())
                ]
            ];

            $channels[] = $channel;
        }

        $this->items = $channels;
        $this->options = $homeRowItem->getSortAndTrimOptions();

        $this->sort();
        $this->trim();

        return $channels;
    }

}
