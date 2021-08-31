<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class NoEmbedContainer extends LiveContainerizer implements ContainerizerInterface
{
    private $homeRowItem;

    public function __construct(HomeRowItem $homeRowItem)
    {
        $this->homeRowItem = $homeRowItem;
    }

    public function getContainers(): Array
    {
        $homeRowItem = $this->homeRowItem;

        $rowName = $homeRowItem->getHomeRow()->getTitle();

        $link = $homeRowItem->getCustomLink();

        $title = $rowName;

        $display = [
            'title' => $title,
            'showArt' => TRUE,
            'showEmbed' => FALSE,
            'showOverlay' => TRUE,
        ];

        $item = [
            'info' => null,
            'broadcast' => null,
            'liveViewerCount' => 0,
            'viewedCount' => 0,
            'showOnline' => TRUE,
            'onlineDisplay' => $display,
            'offlineDisplay' => $display,
            'itemType' => $homeRowItem->getItemType(),
            'rowName' => $rowName,
            'sortIndex' => 1,
            'image' => NULL,
            'overlay' => $this->uploader->asset($this->homeRowItem, 'overlayArtFile'),
            'customArt' => $this->uploader->asset($this->homeRowItem, 'customArtFile'),
            'link' => $link,
            'componentName' => 'NoEmbedContainer',
            'embedName' => NULL,
            'embedData' => NULL,
        ];

        $this->items = Array($item);
        $this->options = $homeRowItem->getSortAndTrimOptions();

        $this->sort();
        $this->trim();

        return $this->items;
    }

}
