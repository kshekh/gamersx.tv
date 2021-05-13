<?php

namespace App\Containerizer;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use Doctrine\Common\Collections\ArrayCollection;

class TwitchGameContainerizer implements ContainerizerInterface
{
    public static function getContainers(HomeRowItem $homeRowItem, $twitch): Array
    {
        $options = $homeRowItem->getContainerizerOptions();
        $gameIds = $options['topic']['topicId'];

        $infos = $twitch->getGameInfo($gameIds);
        $infos = $infos->toArray()['data'];
        $info = $infos[0];

        $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 20);
        $broadcasts = $broadcasts->toArray()['data'];

        switch ($homeRowItem->getLinkType()) {
        case HomeRowItem::LINK_TYPE_GAMERSX:
            $link = '/game/'.$info['id'];
            break;
        case HomeRowItem::LINK_TYPE_TWITCH:
            $link = 'https://www.twitch.tv/directory/game/'.$info['name'];
            break;
        default:
            $link = '#';
        }

        $channels = Array();
        foreach ($broadcasts as $i => $broadcast) {
            $channels[] = [
                'info' => $info,
                'broadcast' => $broadcast,
                'rowType' => 'popular',
                'sortIndex' => $i,
                'image' => NULL,
                'link' => $link,
                'componentName' => 'TwitchContainer',
            ];
        }

        if (array_key_exists('itemSortType', $options)) {
            $sort = $options['itemSortType'];
            if ($sort === HomeRow::SORT_ASC) {
                usort($channels, [ContainerSorter::class, 'sortAscendingPopularity']);
            } elseif ($sort === HomeRow::SORT_DESC) {
                usort($channels, [ContainerSorter::class, 'sortDescendingPopularity']);
            }
        }

        if (array_key_exists('maxEmbeds', $options)) {
            $channels = array_slice($channels, 0, $options['maxEmbeds']);
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

            $channels[0]['image'] = $image;
        }

        return $channels;
    }

}
