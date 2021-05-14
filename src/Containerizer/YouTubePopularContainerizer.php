<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class YouTubePopularContainerizer implements ContainerizerInterface
{
    public static function getContainers($homeRowItem, $youtube): Array
    {
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
        return $channels;
    }

}
