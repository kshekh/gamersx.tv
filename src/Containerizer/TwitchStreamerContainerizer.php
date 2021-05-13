<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class TwitchStreamerContainerizer implements ContainerizerInterface
{
    public static function getContainers(HomeRowItem $homeRowItem, $twitch): Array
    {
        $options = $homeRowItem->getContainerizerOptions();
        $streamerId = $options['topic']['topicId'];

        $info = $twitch->getStreamerInfo($streamerId)->toArray()['data'];
        $broadcast = $twitch->getStreamForStreamer($streamerId)->toArray()['data'];

        return [
            [
                'info' => $info[0],
                'broadcast' => !empty($broadcast) ? $broadcast[0] : null,
                'rowType' => 'popular',
                'sortIndex' => $homeRowItem->getSortIndex(),
                'showArt' => $homeRowItem->getShowArt(),
                'offlineDisplayType' => $homeRowItem->getOfflineDisplayType(),
                'linkType' => $homeRowItem->getLinkType(),
                'componentName' => 'TwitchContainer',
            ]
        ];
    }
}
