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
        $infos = new ArrayCollection($infos->toArray()['data']);

        $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 20);
        $broadcasts = new ArrayCollection($broadcasts->toArray()['data']);

        $channels = Array();

        $info = $infos->first();
        foreach ($broadcasts as $i => $broadcast) {
            $channels[] = [
                'info' => $info,
                'broadcast' => $broadcast,
                'rowType' => 'popular',
                'sortIndex' => $i,
                'showArt' => $homeRowItem->getShowArt(),
                'offlineDisplayType' => $homeRowItem->getOfflineDisplayType(),
                'linkType' => $homeRowItem->getLinkType(),
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

        return $channels;
    }

}
