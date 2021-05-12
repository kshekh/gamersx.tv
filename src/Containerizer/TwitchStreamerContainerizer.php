<?php

namespace App\Containerizer;

class TwitchStreamerContainerizer extends TwitchContainerizer implements ContainerizerInterface
{
    public function getContainers(): Array
    {
        $streamerId = $this->homeRowItem->getContainerizerOptions()['filter']['twitchId'];

        $info = $this->twitch->getStreamerInfo($streamerId)->toArray()['data'];
        $broadcast = $this->twitch->getStreamForStreamer($streamerId)->toArray()['data'];

        return [
            [
                'info' => $info[0],
                'broadcast' => !empty($broadcast) ? $broadcast[0] : null,
                'rowType' => 'popular',
                'sortIndex' => $this->homeRowItem->getSortIndex(),
                'showArt' => $this->homeRowItem->getShowArt(),
                'offlineDisplayType' => $this->homeRowItem->getOfflineDisplayType(),
                'linkType' => $this->homeRowItem->getLinkType(),
            ]
        ];
    }
}
