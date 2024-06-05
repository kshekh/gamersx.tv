<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use App\Service\HomeRowInfo;

class NoEmbedContainer extends LiveContainerizer implements ContainerizerInterface
{
    private HomeRowItem $homeRowItem;

    public function __construct(HomeRowItem $homeRowItem)
    {
        $this->homeRowItem = $homeRowItem;
    }

    public function getContainers(): array
    {
        $homeRowInfo = new HomeRowInfo();
        $homeRowItem = $this->homeRowItem;

        $rowName = $homeRowItem->getHomeRow()->getTitle();

        $link = $homeRowItem->getCustomLink();

        $title = $rowName;
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
                'description' => $description
            ];

            $this->items = Array($item);
            $this->options = $homeRowItem->getSortAndTrimOptions();

            $this->sort();
            $this->trim();

            return $this->items;
        }

        return Array();
    }
}
