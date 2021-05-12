<?php

namespace App\Containerizer;

use Doctrine\Common\Collections\ArrayCollection;

class TwitchGameContainerizer extends TwitchContainerizer implements ContainerizerInterface
{
    public function getContainers(): Array
    {
        $gameIds = $this->homeRowItem->getContainerizerOptions()['filter']['twitchId'];

        $infos = $this->twitch->getGameInfo($gameIds);
        $infos = new ArrayCollection($infos->toArray()['data']);

        $broadcasts = $this->twitch->getTopLiveBroadcastForGame($gameIds, 20);
        $broadcasts = new ArrayCollection($broadcasts->toArray()['data']);

        $channels = Array();

        $info = $infos->first();
        foreach ($broadcasts as $i => $broadcast) {
            $channels[] = [
                'info' => $info,
                'broadcast' => $broadcast,
                'rowType' => 'popular',
                'sortIndex' => $i,
                'showArt' => $this->homeRowItem->getShowArt(),
                'offlineDisplayType' => $this->homeRowItem->getOfflineDisplayType(),
                'linkType' => $this->homeRowItem->getLinkType(),
            ];

        }

        return $channels;
    }

}
