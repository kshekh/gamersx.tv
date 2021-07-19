<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;

class TwitchGameContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    private $homeRowItem;
    private $twitch;

    public function __construct(HomeRowItem $homeRowItem, $twitch)
    {
        $this->homeRowItem = $homeRowItem;
        $this->twitch = $twitch;
    }

    public function getContainers(): Array
    {
        $homeRowItem = $this->homeRowItem;
        $twitch = $this->twitch;
        $gameIds = $homeRowItem->getTopic()['topicId'];

        $infos = $twitch->getGameInfo($gameIds);
        if (200 !== $infos->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$infos->getStatusCode());
            unset($infos);
            return Array();
        }

        $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 20);
        if (200 !== $broadcasts->getStatusCode()) {
            $this->logger->error("Call to Twitch failed with ".$broadcasts->getStatusCode());
            unset($broadcasts);
            return Array();
        }

        // Get the info for the game, use that for every game
        $info = $infos->toArray()['data'][0];

        // Get every broadcast
        $broadcasts = $broadcasts->toArray()['data'];

        $rowName = $homeRowItem->getHomeRow()->getTitle();

        switch ($homeRowItem->getLinkType()) {
        case HomeRowItem::LINK_TYPE_GAMERSX:
            $link = '/game/'.$info['id'];
            break;
        case HomeRowItem::LINK_TYPE_EXTERNAL:
            $link = 'https://www.twitch.tv/directory/game/'.$info['name'];
            break;
        default:
            $link = '#';
        }

        $channels = Array();
        foreach ($broadcasts as $i => $broadcast) {
            $title = sprintf("%s playing %s for %d viewers",
                $broadcast['user_name'], $broadcast['game_name'], $broadcast['viewer_count']);

            $channels[] = [
                'info' => $info,
                'broadcast' => $broadcast,
                'liveViewerCount' => $broadcast['viewer_count'],
                'viewedCount' => 0,
                'showOnline' => TRUE,
                'onlineDisplay' => [
                    'title' => $title,
                    'showArt' => FALSE,
                    'showEmbed' => TRUE,
                ],
                'offlineDisplay' => [
                    'title' => $info['name'],
                    'showArt' => FALSE,
                    'showEmbed' => FALSE,
                ],
                'itemType' => $homeRowItem->getItemType(),
                'rowName' => $rowName,
                'sortIndex' => $i,
                'image' => NULL,
                'customArt' => $this->uploader->asset($this->homeRowItem, 'customArtFile'),
                'link' => $link,
                'componentName' => 'EmbedContainer',
                'embedName' => 'TwitchEmbed',
                'embedData' => [
                    'overlay' => $this->uploader->asset($this->homeRowItem, 'overlayArtFile'),
                    'channel' => $broadcast['user_login'],
                    'elementId' => uniqid('embed-'),
                ],
            ];
        }

        $this->items = $channels;
        $this->options = $homeRowItem->getSortAndTrimOptions();

        $this->sort();

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

            $this->items[0]['image'] = $image;
            $this->items[0]['onlineDisplay']['showArt'] = TRUE;
            $this->items[0]['offlineDisplay']['showArt'] = TRUE;
        }

        $this->trim();

        return $this->items;
    }

}
