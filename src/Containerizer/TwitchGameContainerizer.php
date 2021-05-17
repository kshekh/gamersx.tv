<?php

namespace App\Containerizer;

use App\Entity\HomeRowItem;
use Doctrine\Common\Collections\ArrayCollection;

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
        $infos = $infos->toArray()['data'];
        $info = $infos[0];

        $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 20);
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
                'showLive' => TRUE,
                'liveViewerCount' => $broadcast['viewer_count'],
                'viewedCount' => 0,
                'title' => $title,
                'itemType' => $homeRowItem->getItemType(),
                'rowName' => $rowName,
                'sortIndex' => $i,
                'image' => NULL,
                'showImage' => FALSE,
                'link' => $link,
                'componentName' => 'EmbedContainer',
                'embedName' => 'TwitchEmbed',
                'embedData' => [
                    'channel' => $broadcast['user_login'],
                    'elementId' => 'embed-'.sha1($title),
                ],
            ];
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

        $this->items = $channels;
        $this->options = $homeRowItem->getSortAndTrimOptions();

        $this->sort();
        $this->trim();

        return $this->items;
    }

}
