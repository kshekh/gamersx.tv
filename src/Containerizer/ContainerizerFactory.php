<?php

namespace App\Containerizer;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Service\TwitchApi;
use App\Service\YouTubeApi;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ContainerizerFactory
{
    private $twitch;
    private $youtube;
    private $logger;
    private $uploader;
    private $containerizers;

    public function __construct(TwitchApi $twitch, YouTubeApi $youtube, UploaderHelper $uploader,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->twitch = $twitch;
        $this->youtube = $youtube;
        $this->uploader = $uploader;
        $this->logger = $logger;
    }

    public function __invoke($toBeContainerized): ContainerizerInterface
    {
        if ($toBeContainerized instanceof HomeRowItem) {
            switch($toBeContainerized->getItemType()) {
            case HomeRowItem::TYPE_GAME:
                $containerized = new TwitchGameContainerizer($toBeContainerized, $this->twitch);
                break;
            case HomeRowItem::TYPE_STREAMER:
                $containerized = new TwitchStreamerContainerizer($toBeContainerized, $this->twitch);
                break;
            case HomeRowItem::TYPE_CHANNEL:
                $containerized = new YouTubeChannelContainerizer($toBeContainerized, $this->youtube);
                break;
            case HomeRowItem::TYPE_YOUTUBE:
                $containerized = new YouTubeQueryContainerizer($toBeContainerized, $this->youtube);
                break;
            case HomeRowItem::TYPE_LINK:
                $containerized = new NoEmbedContainer($toBeContainerized);
                break;
            default:
                break;
            }
            $containerized->setLogger($this->logger);
            $containerized->setUploader($this->uploader);
            return $containerized;
        } elseif ($toBeContainerized instanceof HomeRow) {
            return new HomeRowContainerizer($toBeContainerized, $this);
        }
    }


}
